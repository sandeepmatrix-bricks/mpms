@extends('tenant.layouts.master')

@section('title', 'New Record')
@section('page-title', 'New Record')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.records.index', $tenant) }}">Records</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.records._form', ['action' => route('tenant.records.store', $tenant), 'method' => 'POST'])
@endsection
