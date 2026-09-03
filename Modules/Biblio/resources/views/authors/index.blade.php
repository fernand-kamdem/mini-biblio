@extends('layouts.app')

@section('title', __('Authors'))
@section('page-title', __('Authors'))
@section('page-subtitle', __('Manage book authors'))

@section('content')
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:biblio::author-list />
        </div>
    </div>
@endsection
