@extends('admin.layouts.master')

@section('title', 'Edit Page Block')
@section('page-title', 'Edit Page Block')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.page-blocks.index') }}">Page Blocks</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('admin.page-blocks._form', ['action' => route('admin.page-blocks.update', $block), 'method' => 'PUT'])
@endsection
