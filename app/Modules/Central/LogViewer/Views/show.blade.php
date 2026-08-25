@extends('layouts.app')

@section('content')

<div>
    <h1>LogViewer</h1>

    @isset($data)
        {{ $data->id }}
    @endisset
</div>

@endsection