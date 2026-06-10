@extends('company.layouts.master')

@section('title', 'Add Role')
@section('page-title', 'Add Role')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.roles.index') }}">Roles</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('company.roles._form', ['action' => route('company.roles.store'), 'method' => 'POST'])
@endsection
