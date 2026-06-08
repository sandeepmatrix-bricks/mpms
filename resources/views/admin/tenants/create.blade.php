@extends('admin.layouts.master')

@section('title', 'New Tenant')
@section('page-title', 'New Tenant')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.tenants._form', ['action' => route('admin.tenants.store'), 'method' => 'POST'])
@endsection
