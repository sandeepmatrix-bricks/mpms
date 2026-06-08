@extends('admin.layouts.master')

@section('title', 'Assign Membership')
@section('page-title', 'Assign Membership')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.memberships.index') }}">Memberships</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  @include('admin.memberships._form', ['action' => route('admin.memberships.store'), 'method' => 'POST'])
@endsection
