@extends('layouts.app')

@section('title', __('Books'))
@section('page-title', __('Books'))
@section('page-subtitle', __('Browse and manage the library catalogue'))

@section('content')
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:biblio::book-list />
        </div>
    </div>
@endsection
