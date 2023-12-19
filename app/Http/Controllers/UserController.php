<?php

namespace App\Http\Controllers;

use App\Enums\ActionTypes;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('index', [
            'actions' => [
                ActionTypes::Add->value => '+',
                ActionTypes::Subtract->value => '-',
                ActionTypes::Transfer->value => '↦',
            ],
            'users' => User::orderBy('id')->get()
        ]);
    }
}
