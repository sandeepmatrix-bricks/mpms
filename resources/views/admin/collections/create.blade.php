@extends('admin.layouts.master')

@section('title', 'New Collection')
@section('page-title', 'New Collection')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}">Collections</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.collections._form', ['action' => route('admin.collections.store'), 'method' => 'POST'])
@endsection
