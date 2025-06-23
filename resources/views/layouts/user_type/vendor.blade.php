@extends('layouts.app')

@section('vendor')
    @include('layouts.navbars.vendor.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        @include('layouts.navbars.vendor.nav')
        <div class="container-fluid py-4">
            @yield('content')
            @include('layouts.footers.vendor.footer')
        </div>
    </main>
@endsection
