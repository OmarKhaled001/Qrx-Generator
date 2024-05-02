<div class="card mb-0">
    <div class="row g-0 align-items-center">
        <div class="col-lg-5 text-center">
            <img src="{{ asset('assets-dashboard/images/auth/qr3d.png')}}" class="img-fluid" alt="error">
        </div>
        <div class="col-lg-7 mx-auto">
            <div class="card mb-0 border-0 shadow-none mb-0">
                <div class="card-body p-sm-5 m-lg-4">
                    <div class="text-center mt-5">
                        <h5 class="fs-3xl">Welcome Back</h5>
                        <p class="text-muted">Sign in to continue to QrX.</p>
                    </div>
                    <div class="p-2 mt-5">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="position-relative ">
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control " id="email" placeholder="Enter Your Email">
                                    @error('email') <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="float-end">
                                    <a href="#" class="text-muted">Forgot password?</a>
                                </div>
                                <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                <div class="position-relative auth-pass-inputgroup mb-3">
                                    <input type="password" class="form-control pe-5 password-input " name="password" placeholder="Enter Your password" id="password-input">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button class="btn btn-primary w-100" type="submit">Sign In</button>
                            </div>
                        </form>
                        <div class="text-center mt-5">
                            <p class="mb-0">Don't have an account ? <a href="{{ route('register') }}" class="fw-semibold text-secondary text-decoration-underline"> SignUp</a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>