@extends('admin.layouts.master')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
  @include('admin.users._form', ['action' => route('admin.users.update', $user), 'method' => 'PUT'])
@endsection
