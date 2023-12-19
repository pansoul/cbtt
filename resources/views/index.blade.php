@extends('layouts.app')

@section('title', 'Users data')

@section('content')
    <table class="table table-dark table-striped-columns table-hover align-middle m-0">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Balance</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->balance }}</td>
                    <td class="text-end fs-6">
                        <div class="btn-group">
                            @foreach($actions as $actionCode => $actionName)
                                <a
                                    href="{{ route('actions', ['tab' => $actionCode, 'initiator_id' => $user->id]) }}"
                                    class="btn btn-outline-primary"
                                >{{ $actionName }}</a>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
