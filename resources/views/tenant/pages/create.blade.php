@extends('tenant.layouts.master')

@section('title', 'New Page')
@section('page-title', 'New Page')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.pages.index', $tenant) }}">Pages</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.pages._form', ['action' => route('tenant.pages.store', $tenant), 'method' => 'POST'])
@endsection
