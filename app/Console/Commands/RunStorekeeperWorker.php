<?php

namespace App\Console\Commands;

use App\Enums\ActionTypes;
use App\Enums\OperationTypes;
use App\Enums\TransactionHandleTypes;
use App\Models\Operation;
use App\Models\Transaction;
use App\RabbitMQ\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Message\AMQPMessage;

class RunStorekeeperWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:storekeeper-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a worker to consume the Transactions queue';

    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQService)
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        try {
            $rabbitMQService->consume(
                config('rabbitmq.inbox_queue'),
                function (AMQPMessage $msg) use ($rabbitMQService) {
                    $transaction = null;

                    DB::transaction(function () use ($msg, &$transaction) {
                        echo ' [x] Received ', $msg->getBody(), "\n";

                        $transaction = $this->createTransaction($msg);

                        echo " [x] Done\n";

                        $msg->ack();
                    });

                    if ($transaction !== null)
                    {
                        $this->addTransactionToHandleQueue($transaction, $rabbitMQService);
                    }
                }
            );
        } catch (\Throwable $e) {
            cbttlog()->error('Storekeeper worker exception', [
                'msg' => '[' . $e->getCode() . '] ' . $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTrace()
            ]);
            throw $e;
        }
    }

    private function createTransaction(AMQPMessage $msg): Transaction
    {
        $data = json_decode($msg->getBody(), true);
        $execTime = (int)($data['exec_time'] ?? 0);

        if ($execTime > 0) {
            sleep($data['exec_time']);
        }

        $transaction = Transaction::create([
            'msg' => $msg->getBody(),
            'is_approvable' => $data['frozen'] ?? 0,
            'exec_time' => $execTime,
        ]);

        if (isset($data['action_type'])) {
            switch (ActionTypes::from($data['action_type'])) {
                case ActionTypes::Add:
                    Operation::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $data['initiator_id'],
                        'amount' => $data['amount'],
                        'operation_type' => OperationTypes::Add->value,
                    ]);
                    break;

                case ActionTypes::Subtract:
                    Operation::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $data['initiator_id'],
                        'amount' => $data['amount'],
                        'operation_type' => OperationTypes::Subtract->value,
                    ]);
                    break;

                case ActionTypes::Transfer:
                    Operation::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $data['initiator_id'],
                        'amount' => $data['amount'],
                        'operation_type' => OperationTypes::Subtract->value,
                    ]);
                    Operation::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $data['recipient_id'],
                        'amount' => $data['amount'],
                        'operation_type' => OperationTypes::Add->value,
                    ]);
                    break;
            }
        }

        return $transaction;
    }

    private function addTransactionToHandleQueue(Transaction $transaction, RabbitMQService $rabbitMQService): void
    {
        $data = [
            'handle_type' => TransactionHandleTypes::Handle->value,
            'transaction_id' => $transaction->id,
        ];

        $rabbitMQService->publish(config('rabbitmq.handle_queue'), json_encode($data));

        echo ' [x] Sent ', json_encode($data), "\n";

        cbttlog()->info('New message added to the handle queue', $data);
    }
}
