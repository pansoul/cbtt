@extends('layouts.app')

@section('title', 'Transactions data')

@section('content')
    <table class="table table-dark table-striped-columns table-hover align-middle">
        <thead>
        <tr>
            <th>Id</th>
            <th>Status</th>
            <th>Message</th>
            <th>Approve</th>
            <th>Operations</th>
        </tr>
        </thead>
        <tbody class="table-group-divider">
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->status }}</td>
                <td>
                    @php($decodedMsg = json_decode($transaction->msg, true))
                    <textarea style="width: 100%" rows="6" wrap="off" readonly>{{ json_encode($decodedMsg, JSON_PRETTY_PRINT) }}</textarea>
                </td>
                <td>
                    @if($transaction->isFrozen())
                        <div class="btn-group">
                            <a
                                href="{{ route('transactions.approve', [$transaction]) }}"
                                class="btn btn-success"
                            >✓</a>
                            <a
                                href="{{ route('transactions.refund', [$transaction]) }}"
                                class="btn btn-warning"
                            >✕</a>
                        </div>
                    @else
                        &mdash;
                    @endif
                </td>
                <td>
                    <table class="table table-success align-middle m-0">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Operation</th>
                            <th>Result</th>
                        </tr>
                        </thead>
                        <tbody class="table-group-divider">
                        @foreach($transaction->operations as $operation)
                            <tr>
                                <td>{{ $operation->id }}</td>
                                <td>{{ $operation->user->name }}</td>
                                <td>{{ $operation->amount }}</td>
                                <td>{{ $operation->operation_type }}</td>
                                <td>
                                    <span>{{ $operation->is_successful === null ? "?" : ($operation->is_successful ? "✓" : "✕") }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
@endsection
