<?php

namespace App\Enums;

enum TransactionHandleTypes: string
{
    case Handle = 'handle';
    case Approve = 'approve';
    case Refund = 'refund';
}