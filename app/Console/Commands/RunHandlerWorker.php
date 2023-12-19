<?php

namespace App\Console\Commands;

use App\Enums\TransactionHandleTypes;
use App\Models\Transaction;
use App\RabbitMQ\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Message\AMQPMessage;

class RunHandlerWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:handler-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a worker to control the user balance';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQService)
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        try {
            $rabbitMQService->consume(
                config('rabbitmq.handle_queue'),
                function (AMQPMessage $msg) {
                    DB::transaction(function () use ($msg) {
                        echo ' [x] Received ', $msg->getBody(), "\n";

                        $data = json_decode($msg->getBody(), true);
                        $transaction = Transaction::find($data['transaction_id']);

                        match (TransactionHandleTypes::from($data['handle_type'])) {
                            TransactionHandleTypes::Handle => $transaction->handle(),
                            TransactionHandleTypes::Approve => $transaction->approve(),
                            TransactionHandleTypes::Refund => $transaction->refund(),
                        };

                        cbttlog()->info('Transaction was processed', $data);

                        echo " [x] Done\n";

                        $msg->ack();
                    });
                }
            );
        } catch (\Throwable $e) {
            cbttlog()->error('Handler worker exception', [
                'msg' => '[' . $e->getCode() . '] ' . $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTrace()
            ]);
            throw $e;
        }
    }
}
