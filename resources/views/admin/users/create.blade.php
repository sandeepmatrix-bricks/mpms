@extends('admin.layouts.master')

@section('title', 'New User')
@section('page-title', 'New User')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.users._form', ['action' => route('admin.users.store'), 'method' => 'POST'])
@endsection
