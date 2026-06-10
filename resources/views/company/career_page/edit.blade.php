@extends('company.layouts.master')

@section('title', 'Main Page')
@section('page-title', 'Main Page Details')
@section('breadcrumbs')<li class="breadcrumb-item active">Main Page</li>@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Main Page Details</h4>
          <small class="text-muted">Fill up your true details and submit the form.</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('company.career-page.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Page Title</label>
                <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $page->page_title) }}" placeholder="Careers">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Icon Image</label>
                <input type="file" name="icon_image" class="form-control" accept="image/*">
                <small class="text-muted d-block">Note: The file size should be less than 2MB.</small>
                <small class="text-muted d-block">Note: Only files in .jpg, .jpeg, .png, .webp format can be uploaded.</small>
                @if ($page->icon_image)
                  <img src="{{ asset('uploads/careers/'.$page->icon_image) }}" alt="icon" class="img-fluid mt-2" style="max-height:90px;">
                @endif
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Banner Images</label>
                <input type="file" name="banner_image" class="form-control" accept="image/*">
                <small class="text-muted d-block">Note: The file size should be less than 2MB.</small>
                <small class="text-muted d-block">Note: Only files in .jpg, .jpeg, .png, .webp format can be uploaded.</small>
                @if ($page->banner_image)
                  <img src="{{ asset('uploads/careers/'.$page->banner_image) }}" alt="banner" class="img-fluid mt-2" style="max-height:120px;">
                @endif
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Banner Content</label>
                <input type="text" name="banner_content" class="form-control" value="{{ old('banner_content', $page->banner_content) }}" placeholder="Craft Your Career with ...">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" placeholder="Built With Intention">
            </div>

            <div class="mb-3">
              <label class="form-label">Introduction</label>
              <textarea name="introduction" rows="5" class="form-control">{{ old('introduction', $page->introduction) }}</textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Section Heading</label>
              <textarea name="section_heading" rows="2" class="form-control">{{ old('section_heading', $page->section_heading) }}</textarea>
            </div>

            <div class="text-end mt-3">
              <a href="{{ route('company.dashboard') }}" class="btn btn-danger">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
