{{--
  Prévisualisation : change $role → 'admin' | 'bibliothecaire' | 'membre'
  Remplace ensuite les tableaux PHP par les props du composant Livewire.
--}}
@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))

@section('content')

<div class="space-y-6">
   <livewire:dashboard />
</div>
@endsection
