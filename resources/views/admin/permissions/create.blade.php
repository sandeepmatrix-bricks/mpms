@extends('admin.layouts.master')

@section('title', 'New Permission')
@section('page-title', 'New Permission')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.permissions._form', ['action' => route('admin.permissions.store'), 'method' => 'POST'])
@endsection
