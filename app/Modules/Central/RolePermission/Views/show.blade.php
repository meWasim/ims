@extends('layouts.app')

@section('content')

<div>
    <h1>RolePermission</h1>

    @isset($data)
        {{ $data->id }}
    @endisset
</div>

@endsection