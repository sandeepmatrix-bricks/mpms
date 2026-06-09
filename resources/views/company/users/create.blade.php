@extends('company.layouts.master')

@section('title', 'Add User')
@section('page-title', 'Add User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('company.users._form', ['action' => route('company.users.store'), 'method' => 'POST'])
@endsection
