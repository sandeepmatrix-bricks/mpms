@extends('company.layouts.master')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.roles.index') }}">Roles</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.roles._form', ['action' => route('company.roles.update', $role), 'method' => 'PUT'])
@endsection
