@extends('tenant.layouts.master')

@section('title', 'New Collection')
@section('page-title', 'New Collection')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.collections.index', $tenant) }}">Collections</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.collections._form', ['action' => route('tenant.collections.store', $tenant), 'method' => 'POST'])
@endsection
