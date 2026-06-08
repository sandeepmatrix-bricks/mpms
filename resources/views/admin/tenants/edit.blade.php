@extends('admin.layouts.master')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
  <li class="breadcrumb-item active">{{ $tenant->name }}</li>
@endsection

@section('content')
  @include('admin.tenants._form', ['action' => route('admin.tenants.update', $tenant), 'method' => 'PUT'])
@endsection
