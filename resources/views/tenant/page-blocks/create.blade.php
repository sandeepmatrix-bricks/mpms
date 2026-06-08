@extends('tenant.layouts.master')

@section('title', 'New Page Block')
@section('page-title', 'New Page Block')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.page-blocks.index', $tenant) }}">Page Blocks</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('tenant.page-blocks._form', ['action' => route('tenant.page-blocks.store', $tenant), 'method' => 'POST'])
@endsection
