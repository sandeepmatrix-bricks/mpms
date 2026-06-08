@extends('admin.layouts.master')

@section('title', 'Edit Collection')
@section('page-title', 'Edit Collection')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}">Collections</a></li>
  <li class="breadcrumb-item active">{{ $collection->name }}</li>
@endsection

@section('content')
  @include('admin.collections._form', ['action' => route('admin.collections.update', $collection), 'method' => 'PUT'])
@endsection
