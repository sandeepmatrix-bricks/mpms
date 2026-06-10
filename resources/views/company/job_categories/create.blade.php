@extends('company.layouts.master')

@section('title', 'Add Department')
@section('page-title', 'Add Department')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-categories.index') }}">Departments</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('company.job_categories._form', ['action' => route('company.job-categories.store'), 'method' => 'POST'])
@endsection
