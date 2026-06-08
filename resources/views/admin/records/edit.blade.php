@extends('admin.layouts.master')

@section('title', 'Edit Record')
@section('page-title', 'Edit Record')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.records.index') }}">Records</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('admin.records._form', ['action' => route('admin.records.update', $record), 'method' => 'PUT'])
@endsection
