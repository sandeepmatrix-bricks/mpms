@extends('company.layouts.master')

@section('title', 'Add Job Description')
@section('page-title', 'Add Job Description')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-descriptions.index') }}">Job Descriptions</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('company.job_descriptions._form', ['action' => route('company.job-descriptions.store'), 'method' => 'POST'])
@endsection
