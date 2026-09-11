@extends('layouts.app')

@section('title', __('Loans'))
@section('page-title', __('Loans'))
@section('page-subtitle', __('Manage book loans'))


@section('content')

<div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:emprunt::emprunt-list />
        </div>
    </div>
@endsection