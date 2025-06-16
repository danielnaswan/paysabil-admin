@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Student Details</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-md-4">
                    @if($student->user->profile_picture_url)
                        <img src="{{ $student->user->profile_picture_url }}" alt="profile picture" class="img-fluid rounded">
                    @else
                        <img src="/assets/img/default-avatar.png" alt="default profile" class="img-fluid rounded">
                    @endif
                </div>
                <div class="col-md-8">
                    <h5>{{ $student->full_name }}</h5>
                    <p><strong>Matrix No:</strong> {{ $student->matrix_no }}</p>
                    <p><strong>Email:</strong> {{ $student->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $student->user->phone_number }}</p>
                    <p><strong>Created:</strong> {{ $student->created_at->format('d/m/Y') }}</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('student.edit', $student->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('student.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection