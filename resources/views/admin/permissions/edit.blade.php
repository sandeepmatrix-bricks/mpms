@extends('admin.layouts.master')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
  <li class="breadcrumb-item active">{{ $permission->key }}</li>
@endsection

@section('content')
  @include('admin.permissions._form', ['action' => route('admin.permissions.update', $permission), 'method' => 'PUT'])
@endsection
