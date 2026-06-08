@extends('admin.layouts.master')

@section('title', 'New Page')
@section('page-title', 'New Page')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Pages</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.pages._form', ['action' => route('admin.pages.store'), 'method' => 'POST'])
@endsection
