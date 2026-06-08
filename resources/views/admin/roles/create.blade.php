@extends('admin.layouts.master')

@section('title', 'New Role')
@section('page-title', 'New Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.roles._form', ['action' => route('admin.roles.store'), 'method' => 'POST'])
@endsection
