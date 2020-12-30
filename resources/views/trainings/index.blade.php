@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Dashboard Trainings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard Trainings</li>
    </ol>
    <div class="row">
        <div class="col-md-12">
            @if( session()->has('alert'))
                <div class="alert {{ session()->get('alert-type') }}">
                    {{ session()->get('alert') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    {{ __('Training Index') }}

                    <div class="float-right">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control"/>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Creator</th>
                                <th>Created At</th>
                                <th>Created DateTime</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainings as $training)
                                <tr>
                                    <td>{{ $training->id }}</td>
                                    <td>{{ $training->title }}</td>
                                    <td>{{ $training->description }}</td>
                                    <td>
                                        {{ $training->user->name }}
                                        <strong>
                                            ({{ $training->user->email }})
                                        </strong>
                                    </td>
                                    <td>{{ $training->created_at ? $training->created_at->diffForHumans() : 'NO DATA' }}</td>
                                    <td>{{ $training->created_at ?? '-' }}</td>
                                    <td>
                                        @can('view', $training)
                                            <a href="{{ route('training:show', $training) }}" class="btn btn-primary">View</a>
                                        @endcan
                                        @can('update', $training)
                                            <a href="{{ route('training:edit', $training) }}" class="btn btn-success">Edit</a>
                                        @endcan
                                        @can('delete', $training)
                                            <a onclick="return confirm('Are you sure?')" href="{{ route('training:delete', $training) }}" class="btn btn-danger">Delete</a>
                                            <hr>
                                            <a onclick="return confirm('Are you sure WANT TO PERMANENTLY DELETE?')" href="{{ route('training:forceDelete', $training) }}" class="btn btn-lg btn-danger">FORCE DELETE</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $trainings
                        ->appends([
                                'keyword' => request()->get('keyword')
                            ])
                        ->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
