@extends('admin.layouts.master')

@section('title', 'New Record')
@section('page-title', 'New Record')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.records.index') }}">Records</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.records._form', ['action' => route('admin.records.store'), 'method' => 'POST'])
@endsection
