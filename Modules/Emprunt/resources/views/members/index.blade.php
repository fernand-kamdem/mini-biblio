@extends('layouts.app')

@section('title', __('Members'))
@section('page-title', __('Members'))
@section('page-subtitle', __('Manage book members'))


@section('content')

<div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:emprunt::member-list />
        </div>
    </div>
@endsection