@extends('admin.layouts.master')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
  <li class="breadcrumb-item active">{{ $role->name }}</li>
@endsection

@section('content')
  @include('admin.roles._form', ['action' => route('admin.roles.update', $role), 'method' => 'PUT'])
@endsection
