@extends('tenant.layouts.master')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.users.index', $tenant) }}">Team</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('tenant.users._form', ['action' => route('tenant.users.update', [$tenant, $user]), 'method' => 'PUT'])
@endsection
