<?php

namespace App\Models;

use App\Enums\OperationTypes;
use App\Enums\TransactionStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'status',
        'exec_time',
        'msg',
        'is_approvable',
    ];

    public function changeStatus(TransactionStatuses $status): void
    {
        $this->update([
            'status' => $status->value
        ]);
    }

    public function isNew(): bool
    {
        return $this->status === TransactionStatuses::New->value;
    }

    public function isFrozen(): bool
    {
        return $this->status === TransactionStatuses::Frozen->value;
    }

    public function isSuccessful(): bool
    {
        return $this->status === TransactionStatuses::Successful->value;
    }

    public function isFailed(): bool
    {
        return $this->status === TransactionStatuses::Failed->value;
    }

    public function inTerminalStatus(): bool
    {
        return in_array(
            $this->status,
            [
                TransactionStatuses::Successful->value,
                TransactionStatuses::Failed->value,
                TransactionStatuses::Refunded->value
            ],
            true
        );
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function handle(): void
    {
        $userModels = [];
        $isTransactionFailed = false;

        if ($this->operations->isEmpty()) {
            $isTransactionFailed = true;
        }

        if ($this->exec_time > 0) {
            sleep($this->exec_time);
        }

        if (!$isTransactionFailed) {
            foreach ($this->operations as $operation) {
                $user = User::query()->lockForUpdate()->find($operation->user_id);
                $userModels[] = $user;
                $isOperationFailed = false;

                switch (OperationTypes::tryFrom($operation->operation_type)) {
                    case OperationTypes::Add:
                        $user->balance += $operation->amount;
                        break;

                    case OperationTypes::Subtract:
                        if ($user->balance < $operation->amount) {
                            $isTransactionFailed = true;
                            $isOperationFailed = true;
                        } else {
                            $user->balance -= $operation->amount;
                        }
                        break;

                    default:
                        $isTransactionFailed = true;
                        $isOperationFailed = true;
                        break;
                }

                $operation->is_successful = !$isOperationFailed;
                $operation->saveOrFail();

                if ($isTransactionFailed) {
                    break;
                }
            }
        }

        $successStatus = $this->is_approvable ? TransactionStatuses::Frozen->value : TransactionStatuses::Successful->value;
        $this->status = $isTransactionFailed ? TransactionStatuses::Failed->value : $successStatus;
        $this->saveOrFail();

        if (!$isTransactionFailed) {
            foreach ($userModels as $model) {
                $model->saveOrFail();
            }
        }
    }

    public function refund(): void
    {
        if (!$this->isFrozen())
        {
            throw new \Exception('This transaction cannot be refunded');
        }

        $userModels = [];
        $isTransactionFailed = false;

        if ($this->operations->isEmpty()) {
            $isTransactionFailed = true;
        }

        if (!$isTransactionFailed) {
            foreach ($this->operations as $operation) {
                $user = User::query()->lockForUpdate()->find($operation->user_id);
                $userModels[] = $user;
                $isOperationFailed = false;

                switch (OperationTypes::tryFrom($operation->operation_type)) {
                    case OperationTypes::Subtract:
                        $user->balance += $operation->amount;
                        break;

                    default:
                        $isTransactionFailed = true;
                        $isOperationFailed = true;
                        break;
                }

                $operation->is_successful = !$isOperationFailed;
                $operation->saveOrFail();

                if ($isTransactionFailed) {
                    break;
                }
            }
        }

        $this->status = $isTransactionFailed ? TransactionStatuses::Failed->value : TransactionStatuses::Refunded->value;
        $this->saveOrFail();

        if (!$isTransactionFailed) {
            foreach ($userModels as $model) {
                $model->saveOrFail();
            }
        }
    }

    public function approve(): void
    {
        if (!$this->isFrozen())
        {
            throw new \Exception('This transaction cannot be approved');
        }

        $this->changeStatus(TransactionStatuses::Successful);
    }
}
