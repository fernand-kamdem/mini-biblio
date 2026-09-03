@extends('layouts.app')

@section('title', __('Categories'))
@section('page-title', __('Categories'))
@section('page-subtitle', __('Manage book categories'))

@section('content')
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <livewire:biblio::category-list />
        </div>
    </div>
@endsection
