@extends('layouts.app')

@section('title', __('My loans'))
@section('page-title', __('My loans'))
@section('page-subtitle', __('View and manage your loans'))

@section('content')
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:emprunt::mes-emprunts />
        </div>
    </div>
@endsection