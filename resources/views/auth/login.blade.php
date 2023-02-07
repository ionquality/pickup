@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 p-0">
        <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
          <img src="https://accuratesteeltreating.com/wp-content/uploads/2017/10/new-bg-rev.jpg"
               alt="auth-login-cover" class="img-fluid my-5 auth-illustration">

          <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}"
               alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
               data-app-dark-img="illustrations/bg-shape-image-dark.png">
        </div>
      </div>
      <!-- /Left Text -->

      <!-- Login -->
      <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-5">
            <a href="{{url('/')}}" class="app-brand-link">
              <span
                class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
              <span class="app-brand-text demo text-body fw-bold">{{config('variables.templateName')}}</span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-2">Welcome to AST Pickup List! 👋</h4>
          <p class="mb-4">Please sign-in using your Cornerstone password</p>

          @if (session('status'))
            <div class="alert alert-success mb-1 rounded-0" role="alert">
              <div class="alert-body">
                {{ session('status') }}
              </div>
            </div>
          @endif

          <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="a@a.com">
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="login-password">Password</label>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="login-password" class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                       aria-describedby="password"/>
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                @error('password')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me"
                       name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember-me">
                  Remember Me
                </label>
              </div>
            </div>
            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
          </form>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
@endsection