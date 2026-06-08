@extends('tenant.layouts.master')

@section('title', 'Edit Record')
@section('page-title', 'Edit Record')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.records.index', $tenant) }}">Records</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('tenant.records._form', ['action' => route('tenant.records.update', [$tenant, $record]), 'method' => 'PUT'])
@endsection
