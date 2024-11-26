@extends('layouts.login')

@section('content')
<div class="container-fluid">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="text-center">
                    <img src="{{ asset('assets/img/LOGO2.png') }}" alt="Logo" class="img-fluid" style="max-width: 50%; margin: 20px auto;">
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900">Welcome Back!</h1>
                    </div>

                    <!-- Display error message if credentials are invalid -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif (session('no_account'))
                        <!-- Display a message if there is no account associated with the email -->
                        <div class="alert alert-warning">
                            {{ session('no_account') }}
                        </div>
                    @endif

                    <form class="user" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Email Address" name="email" value="{{ old('email', session('login_email')) }}" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" name="password" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="remember" id="remember">
                                <label class="custom-control-label" for="remember">Remember Me</label>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-success btn-user btn-block">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
