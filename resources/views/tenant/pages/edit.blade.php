@extends('tenant.layouts.master')

@section('title', 'Edit Page')
@section('page-title', 'Edit Page')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.pages.index', $tenant) }}">Pages</a></li>
  <li class="breadcrumb-item active">{{ $page->title }}</li>
@endsection

@section('content')
  @include('tenant.pages._form', ['action' => route('tenant.pages.update', [$tenant, $page]), 'method' => 'PUT'])
@endsection
