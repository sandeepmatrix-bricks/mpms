@extends('admin.layouts.master')

@section('title', 'Edit Sub Admin')
@section('page-title', 'Edit Sub Admin')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.sub-admins.index') }}">Sub Admins</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('admin.sub-admins._form', ['action' => route('admin.sub-admins.update', $user), 'method' => 'PUT'])
@endsection
