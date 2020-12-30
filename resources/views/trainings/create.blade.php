@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Create Trainings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard Trainings</li>
    </ol>
    <div class="row">
        <div class="col-md-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Create Training') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('training:store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required class="@error('title') is-invalid @enderror" value="{{ old('title') }}">
                            @error('title')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" class="@error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Trainer</label>
                            <input type="text" name="trainer" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Attachment</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Store My Training</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
