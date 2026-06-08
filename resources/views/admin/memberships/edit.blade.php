@extends('admin.layouts.master')

@section('title', 'Edit Membership')
@section('page-title', 'Edit Membership')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.memberships.index') }}">Memberships</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  @include('admin.memberships._form', ['action' => route('admin.memberships.update', $membership), 'method' => 'PUT'])
@endsection
