@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<h1>Dashboard Admin</h1>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>
@endsection
