@extends('layouts.app')

@section('title', __('Librarians'))
@section('page-title', __('Librarians'))
@section('page-subtitle', __('Manage librarians'))


@section('content')

<div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:emprunt::librarian-list />
        </div>
</div>
@endsection