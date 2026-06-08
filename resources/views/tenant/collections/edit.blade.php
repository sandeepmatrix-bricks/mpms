@extends('tenant.layouts.master')

@section('title', 'Edit Collection')
@section('page-title', 'Edit Collection')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.collections.index', $tenant) }}">Collections</a></li>
  <li class="breadcrumb-item active">{{ $collection->name }}</li>
@endsection

@section('content')
  @include('tenant.collections._form', ['action' => route('tenant.collections.update', [$tenant, $collection]), 'method' => 'PUT'])
@endsection
