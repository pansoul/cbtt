@php
    use App\Enums\ActionTypes;

    $selectedTab = old('action_type', request()->get('tab') ?? ActionTypes::Add->value);
    $selectedInitiator = request()->get('initiator_id');
@endphp

@extends('layouts.app')

@section('title', 'Create a new transaction')

@section('content')
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        @foreach ($tabs as $tabCode => $tabName)
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link @if($selectedTab == $tabCode) active @endif"
                    data-bs-toggle="tab" data-bs-target="#{{ $tabCode }}-tab"
                    type="button"
                >
                    {{ $tabName }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade @if($selectedTab == ActionTypes::Add->value) active show @endif" id="{{ ActionTypes::Add->value }}-tab">
            @if ($errors->has('add.*'))
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('actions.add') }}" novalidate>
                @csrf
                <input type="hidden" name="action_type" value="{{ ActionTypes::Add->value }}">
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select name="add[initiator_id]" class="form-select">
                        @foreach ($users as $user)
                            <option @if(old('subtract.initiator_id', $selectedInitiator) == $user->id) selected @endif value="{{ $user->id }}">
                                {{ $user->name }}: {{ $user->balance }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="add[amount]" class="form-control" value="{{ old('add.amount') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Execution time in sec (for testing purpose)</label>
                    <input type="number" name="add[exec_time]" class="form-control" value="{{ old('add.exec_time') }}">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>

        <div class="tab-pane fade @if($selectedTab == ActionTypes::Subtract->value) active show @endif" id="{{ ActionTypes::Subtract->value }}-tab">
            @if ($errors->has('subtract.*'))
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('actions.subtract') }}" novalidate>
                @csrf
                <input type="hidden" name="action_type" value="{{ ActionTypes::Subtract->value }}">
                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select name="subtract[initiator_id]" class="form-select">
                        @foreach ($users as $user)
                            <option @if(old('subtract.initiator_id', $selectedInitiator) == $user->id) selected @endif value="{{ $user->id }}">
                                {{ $user->name }}: {{ $user->balance }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="subtract[amount]" class="form-control"
                           value="{{ old('subtract.amount') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Execution time in sec (for testing purpose)</label>
                    <input type="number" name="subtract[exec_time]" class="form-control"
                           value="{{ old('subtract.exec_time') }}">
                </div>
                <div class="mb-3 form-check">
                    <input
                        name="subtract[frozen]"
                        @if(old('subtract.frozen', false)) checked @endif
                        type="checkbox"
                        class="form-check-input"
                        id="frozen"
                    >
                    <label class="form-check-label" for="frozen">Frozen</label>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>

        <div class="tab-pane fade @if($selectedTab == ActionTypes::Transfer->value) active show @endif" id="{{ ActionTypes::Transfer->value }}-tab">
            @if ($errors->has('transfer.*'))
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('actions.transfer') }}" novalidate>
                @csrf
                <input type="hidden" name="action_type" value="{{ ActionTypes::Transfer->value }}">
                <div class="mb-3">
                    <label class="form-label">From user</label>
                    <select name="transfer[initiator_id]" class="form-select">
                        @foreach ($users as $user)
                            <option @if(old('transfer.initiator_id', $selectedInitiator) == $user->id) selected @endif value="{{ $user->id }}">
                                {{ $user->name }}: {{ $user->balance }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">To user</label>
                    <select name="transfer[recipient_id]" class="form-select">
                        @foreach ($users as $user)
                            <option @if(old('transfer.recipient_id') == $user->id) selected @endif value="{{ $user->id }}">
                                {{ $user->name }}: {{ $user->balance }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="transfer[amount]" class="form-control"
                           value="{{ old('transfer.amount') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Execution time in sec (for testing purpose)</label>
                    <input type="number" name="transfer[exec_time]" class="form-control"
                           value="{{ old('transfer.exec_time') }}">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection
