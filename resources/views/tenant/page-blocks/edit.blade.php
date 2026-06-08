@extends('tenant.layouts.master')

@section('title', 'Edit Page Block')
@section('page-title', 'Edit Page Block')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('tenant.page-blocks.index', $tenant) }}">Page Blocks</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('tenant.page-blocks._form', ['action' => route('tenant.page-blocks.update', [$tenant, $block]), 'method' => 'PUT'])
@endsection
