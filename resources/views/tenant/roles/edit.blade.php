@extends('tenant.layouts.master')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.roles.index', $tenant) }}">Roles</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('tenant.roles._form', ['action' => route('tenant.roles.update', [$tenant, $role]), 'method' => 'PUT'])
@endsection
