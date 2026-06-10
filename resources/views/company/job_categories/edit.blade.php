@extends('company.layouts.master')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-categories.index') }}">Departments</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.job_categories._form', ['action' => route('company.job-categories.update', $category), 'method' => 'PUT'])
@endsection
