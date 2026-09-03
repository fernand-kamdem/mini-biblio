@extends('layouts.app')

@section('title', __('Books'))
@section('page-title', __('Books'))
@section('page-subtitle', __('Browse and manage the library catalogue'))

@section('content')
    <livewire:biblio::book-list />
@endsection
