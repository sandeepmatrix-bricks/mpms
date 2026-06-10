@extends('company.layouts.master')

@section('title', 'Add Status')
@section('page-title', 'Add Status')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.statuses.index') }}">Statuses</a></li>
  <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
  @include('company.statuses._form', ['action' => route('company.statuses.store'), 'method' => 'POST'])
@endsection
