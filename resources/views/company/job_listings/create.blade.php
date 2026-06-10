@extends('company.layouts.master')

@section('title', 'Add Designation')
@section('page-title', 'Add Designation')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.job-listings.index') }}">Designations</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('company.job_listings._form', ['action' => route('company.job-listings.store'), 'method' => 'POST'])
@endsection
