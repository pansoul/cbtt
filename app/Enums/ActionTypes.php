<?php

namespace App\Enums;

enum ActionTypes: string
{
    case Add = 'add';
    case Subtract = 'subtract';
    case Transfer = 'transfer';
}