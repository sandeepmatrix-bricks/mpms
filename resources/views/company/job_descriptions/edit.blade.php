@extends('company.layouts.master')

@section('title', 'Edit Job Description')
@section('page-title', 'Edit Job Description')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-descriptions.index') }}">Job Descriptions</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.job_descriptions._form', ['action' => route('company.job-descriptions.update', $description), 'method' => 'PUT'])
@endsection
