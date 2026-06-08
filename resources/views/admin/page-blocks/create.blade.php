@extends('admin.layouts.master')

@section('title', 'New Page Block')
@section('page-title', 'New Page Block')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.page-blocks.index') }}">Page Blocks</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.page-blocks._form', ['action' => route('admin.page-blocks.store'), 'method' => 'POST'])
@endsection
