@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center card-img">
                            <img src="{{ asset("images/login_center_logo.svg") }}" alt="" class="head-logo">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="col-form-label text-md-end">{{ __('MAIL') }}</label>

                            <div>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="password" class="col-form-label text-md-end">{{ __('PASS') }}</label>

                            <div>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-0">
                            <div>
                                <button type="submit" class="btn">
                                    {{ __('ログイン') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center card-img">
        <img src="{{ asset("images/login_footer_logo.svg") }}" alt="" class="head-logo">
    </div>
</div>
@endsection

<style>
    .card {
        height: 440px;
        padding: 24px 60px;
        
    }
    .btn {
        width: 100%;
        background: linear-gradient(90deg, #30CFC7 9.11%, #3A7EBA 89.29%) !important;
        border-radius: 62px !important;
        color: #ffffff !important;
        height: 48px;
    }
    .card-img {
        margin-top: -20px;
    }
</style>
