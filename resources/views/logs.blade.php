@extends('layouts.app')

@section('title', 'Events logs')

@section('content')
    <textarea class="form-control" rows="15" wrap="off" readonly>@if(is_file($logfile)){{ file_get_contents($logfile) }}@endif</textarea>
@endsection
