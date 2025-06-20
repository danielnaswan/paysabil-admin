@extends('layouts.user_type.guest')

@section('content')
    <section class="min-vh-100 mb-8">
        <div class="page-header align-items-start min-vh-50 pt-5 pb-11 mx-3 border-radius-lg"
            style="background-image: url('../assets/img/uthm-images/uthm-ptta-banner.png');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 text-center mx-auto mt-4">
                        <h1 class="text-white mb-2 mt-5">Hi, Welcome!</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10">
                <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            <h5>Registration Form</h5>
                        </div>
                        <div class="card-body">
                            <form role="form text-left" method="POST" action="/register">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Name" name="business_name"
                                        id="business_name" aria-label="Business Name" aria-describedby="name"
                                        value="{{ old('business_name') }}" required>
                                    @error('business_name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Email" name="email"
                                        id="email" aria-label="Email" aria-describedby="email-addon"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="service_category"
                                        id="service_category" aria-label="Service Category" aria-describedby="service-cat"
                                        value="Makanan" readonly>
                                    @error('service_category')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="tel" value="{{old('phone_number')}}"
                                     id="phone_number" name="phone_number" placeholder="Phone number" required>
                                     @error('phone_number')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                     @enderror
                                </div>
                                <div class="mb-3">
                                    
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="file" id="profile_picture" name="profile_picture" accept="image/*">
                                    @error('profile_picture')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control" placeholder="Password" name="password"
                                        id="password" aria-label="Password" aria-describedby="password-addon" required>
                                    @error('password')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="password" id="password_confirmation"
                                    placeholder="Password Confirmation" name="password_confirmation" required>
                                </div>
                                <div class="form-check form-check-info text-left">
                                    <input class="form-check-input" type="checkbox" name="agreement"
                                        id="flexCheckDefault" checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and
                                            Conditions</a>
                                    </label>
                                    @error('agreement')
                                        <p class="text-danger text-xs mt-2">First, agree to the Terms and Conditions, then try
                                            register again.</p>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign up</button>
                                </div>
                                <p class="text-sm mt-3 mb-0">Already have an account? <a href="login"
                                        class="text-dark font-weight-bolder">Sign in</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
