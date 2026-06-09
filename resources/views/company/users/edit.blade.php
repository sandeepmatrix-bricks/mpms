@extends('company.layouts.master')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.users._form', ['action' => route('company.users.update', $user), 'method' => 'PUT'])
@endsection
