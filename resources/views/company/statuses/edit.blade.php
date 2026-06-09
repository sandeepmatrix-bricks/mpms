@extends('company.layouts.master')

@section('title', 'Edit Status')
@section('page-title', 'Edit Status')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.statuses.index') }}">Statuses</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.statuses._form', ['action' => route('company.statuses.update', $status), 'method' => 'PUT'])
@endsection
