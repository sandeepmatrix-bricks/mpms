@extends('admin.layouts.master')

@section('title', 'Edit Page')
@section('page-title', 'Edit Page')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Pages</a></li>
  <li class="breadcrumb-item active">{{ $page->title }}</li>
@endsection

@section('content')
  @include('admin.pages._form', ['action' => route('admin.pages.update', $page), 'method' => 'PUT'])
@endsection
