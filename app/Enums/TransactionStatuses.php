<?php

namespace App\Enums;

enum TransactionStatuses: string
{
    case New = 'new';
    case Frozen = 'frozen';
    case Successful = 'successful';
    case Failed = 'failed';
    case Refunded = 'refunded';
}