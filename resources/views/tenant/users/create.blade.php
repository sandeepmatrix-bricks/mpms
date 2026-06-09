@extends('tenant.layouts.master')

@section('title', 'Add User')
@section('page-title', 'Add User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.users.index', $tenant) }}">Team</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.users._form', ['action' => route('tenant.users.store', $tenant), 'method' => 'POST'])
@endsection
