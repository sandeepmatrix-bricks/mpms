@extends('tenant.layouts.master')

@section('title', 'Add Role')
@section('page-title', 'Add Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.roles.index', $tenant) }}">Roles</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.roles._form', ['action' => route('tenant.roles.store', $tenant), 'method' => 'POST'])
@endsection
