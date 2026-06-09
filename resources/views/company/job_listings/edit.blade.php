@extends('company.layouts.master')

@section('title', 'Edit Designation')
@section('page-title', 'Edit Designation')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-listings.index') }}">Designations</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('company.job_listings._form', ['action' => route('company.job-listings.update', $listing), 'method' => 'PUT'])
@endsection
