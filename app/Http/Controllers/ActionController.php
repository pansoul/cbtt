<?php

namespace App\Http\Controllers;

use App\Enums\ActionTypes;
use App\Http\Requests\AddActionFormRequest;
use App\Http\Requests\SubtractActionFormRequest;
use App\Http\Requests\TransferActionFormRequest;
use App\Models\User;
use App\RabbitMQ\RabbitMQService;

class ActionController extends Controller
{
    public function page()
    {
        return view('actions', [
            'tabs' => [
                ActionTypes::Add->value => ucfirst(ActionTypes::Add->value) . ' (+)',
                ActionTypes::Subtract->value => ucfirst(ActionTypes::Subtract->value) . ' (-)',
                ActionTypes::Transfer->value => ucfirst(ActionTypes::Transfer->value) . ' (↦)',
            ],
            'users' => User::orderBy('id')->get()
        ]);
    }

    public function addAction(AddActionFormRequest $request, RabbitMQService $rabbitMQService)
    {
        $data = [
            'action_type' => ActionTypes::Add->value,
            'initiator_id' => $request->validated('add.initiator_id'),
            'amount' => $request->validated('add.amount'),
            'exec_time' => $request->validated('add.exec_time'),
        ];

        $rabbitMQService->publish(config('rabbitmq.inbox_queue'), json_encode($data));

        flash()->success('A transaction for balance addition has been added to the inbox queue');

        cbttlog()->info('New message added to the inbox queue', $data);

        return back();
    }

    public function subtractAction(SubtractActionFormRequest $request, RabbitMQService $rabbitMQService)
    {
        $data = [
            'action_type' => ActionTypes::Subtract->value,
            'initiator_id' => $request->validated('subtract.initiator_id'),
            'amount' => $request->validated('subtract.amount'),
            'frozen' => (int)$request->get('subtract.frozen'),
            'exec_time' => $request->validated('subtract.exec_time'),
        ];

        $rabbitMQService->publish(config('rabbitmq.inbox_queue'), json_encode($data));

        flash()->success('A transaction for balance subtraction has been added to the inbox queue');

        cbttlog()->info('New message added to the inbox queue', $data);

        return back();
    }

    public function transferAction(TransferActionFormRequest $request, RabbitMQService $rabbitMQService)
    {
        $data = [
            'action_type' => ActionTypes::Transfer->value,
            'initiator_id' => $request->validated('transfer.initiator_id'),
            'recipient_id' => $request->validated('transfer.recipient_id'),
            'amount' => $request->validated('transfer.amount'),
            'exec_time' => $request->validated('transfer.exec_time'),
        ];

        $rabbitMQService->publish(config('rabbitmq.inbox_queue'), json_encode($data));

        flash()->success('A transaction for balance transfer has been added to the inbox queue');

        cbttlog()->info('New message added to the inbox queue', $data);

        return back();
    }
}
