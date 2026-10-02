@extends('layouts.barista')

@section('title', 'Pengajuan Restock')

@push('styles')
    @vite('resources/css/barista/restock-requests.css')
@endpush

@section('content')<h1>Dashboard Barista</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">
            Logout
        </button>
    </form>
@endsection
