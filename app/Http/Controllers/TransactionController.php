<?php

namespace App\Http\Controllers;

use App\Enums\TransactionHandleTypes;
use App\Models\Transaction;
use App\RabbitMQ\RabbitMQService;

class TransactionController extends Controller
{
    public function page()
    {
        return view('transactions', [
            'transactions' => Transaction::with('operations.user')->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function approveAction(Transaction $transaction, RabbitMQService $rabbitMQService)
    {
        if ($transaction->isFrozen()) {
            $data = [
                'handle_type' => TransactionHandleTypes::Approve->value,
                'transaction_id' => $transaction->id,
            ];

            $rabbitMQService->publish(config('rabbitmq.handle_queue'), json_encode($data));

            flash()->success('A transaction for balance approving has been added to the handle queue');

            cbttlog()->info('New message added to the handle queue', $data);
        }

        return redirect(route('transactions'));
    }

    public function refundAction(Transaction $transaction, RabbitMQService $rabbitMQService)
    {
        if ($transaction->isFrozen()) {
            $data = [
                'handle_type' => TransactionHandleTypes::Refund->value,
                'transaction_id' => $transaction->id,
            ];

            $rabbitMQService->publish(config('rabbitmq.handle_queue'), json_encode($data));

            flash()->success('A transaction for balance refunding has been added to the handle queue');

            cbttlog()->info('New message added to the handle queue', $data);
        }

        return redirect(route('transactions'));
    }
}
