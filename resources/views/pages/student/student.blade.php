@extends('layouts.user_type.auth')

@section('content')

<div>
    @if(session('success'))
        <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
            <span class="alert-text text-white">
            {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">All Students</h5>
                        </div>
                        <a href="{{ route('student.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+ New Student</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        No
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        PHOTO
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        NAME
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        MATRIX NO
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        EMAIL
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        CREATION DATE
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        ACTION
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <div>
                                            @if($student->user->profile_picture_url)
                                                <img src="{{ $student->user->profile_picture_url }}" class="avatar avatar-sm me-3">
                                            @else
                                                <img src="{{ asset('assets/img/default-avatar.png') }}" class="avatar avatar-sm me-3">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $student->full_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $student->matrix_no }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $student->user->email }}</p>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs font-weight-bold">{{ $student->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="ms-auto">
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('student.show', $student->id) }}">
                                                <i class="fas fa-eye text-dark me-2"></i>View
                                            </a>
                                            <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('student.edit', $student->id) }}">
                                                <i class="fas fa-pencil-alt text-dark me-2"></i>Edit
                                            </a>
                                            <form action="{{ route('student.destroy', $student->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this student?')">
                                                    <i class="far fa-trash-alt me-2"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection