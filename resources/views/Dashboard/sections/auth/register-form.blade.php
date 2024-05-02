<div class="card mb-0">
    <div class="row g-0 align-items-center">
        <div class="col-lg-5 text-center">
            <img src="{{ asset('assets-dashboard/images/auth/qr-3d.png')}}" class="img-fluid" alt="error">
        </div>
        <div class="col-lg-7 mx-auto">
            <div class="card mb-0 border-0 shadow-none mb-0">
                <div class="card-body p-sm-5 m-lg-4">
                    <div class="text-center mt-5">
                        <h5 class="fs-3xl">Register</h5>
                        <p class="text-muted">Sign Up to continue to QrX.</p>
                    </div>
                    <div class="p-2 mt-5">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="position-relative ">
                                    <input type="text" class="form-control  password-input" name="name" value="{{ old('name') }}" placeholder="Enter Your Full Name">
                                </div>
                                @error('name') <p class="text-danger">{{$message}}</p> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Phone <span class="text-danger">*</span></label>
                                <div class="position-relative ">
                                    <input type="text" class="form-control" id="cleave-phone" name="phone" value="{{ old('phone') }}" placeholder="(+20) xxx-xxxx">                                                    
                                </div>
                                    @error('phone') <p class="text-danger">{{$message}}</p> @enderror
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <label for="name" class="form-label"  >Address <span class="text-danger">*</span></label>
                                    <div class="col-lg-4 col-sm-12">
                                        <input type="text" class="form-control  password-input" name="country" value="{{ old('country') }}" placeholder="Country">
                                        @error('country') <p class="text-danger">{{$message}}</p> @enderror
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <input type="text" class="form-control  password-input" name="city" value="{{ old('city') }}" placeholder="City">
                                        @error('city') <p class="text-danger">{{$message}}</p> @enderror
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <input type="text" class="form-control  password-input" name="area" value="{{ old('area') }}" placeholder="Area">
                                        @error('area') <p class="text-danger">{{$message}}</p> @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="position-relative ">
                                    <input type="email" class="form-control " name="email" value="{{ old('email') }}"placeholder="Enter Your Email">
                                </div>
                                @error('email') <p class="text-danger">{{$message}}</p> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                <div class="position-relative auth-pass-inputgroup mb-3">
                                    <input type="password" class="form-control pe-5 password-input " name="password" placeholder="Enter password" id="password-input">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="position-relative auth-pass-inputgroup mb-3">
                                    <input type="password" class="form-control pe-5 password-input " name="password_confirmation" placeholder="Enter  confirma password" id="password-input">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                <label class="form-check-label" for="auth-remember-check">Remember me</label>
                            </div>
                            <div class="mt-4">
                                <button class="btn btn-primary w-100" type="submit">Sign In</button>
                            </div>
                        </form>
                        <div class="text-center mt-5">
                            <p class="mb-0">You already have an account ? <a href="{{route('login')}}" class="fw-semibold text-secondary text-decoration-underline"> Sign In</a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>