@extends('admin.layouts.master')

@section('title', 'Add Sub Admin')
@section('page-title', 'Add Sub Admin')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.sub-admins.index') }}">Sub Admins</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.sub-admins._form', ['action' => route('admin.sub-admins.store'), 'method' => 'POST'])
@endsection
