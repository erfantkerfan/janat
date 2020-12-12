@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('navbar.Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

{{--                        @include('auth.registerField', ['key' => 'f_name'])--}}

                        @include('auth.registerField', ['key' => 'f_name', 'label' => 'نام'])
                        @include('auth.registerField', ['key' => 'l_name', 'label' => 'نام خانوادگی'])
                        @include('auth.registerField', ['key' => 'SSN', 'label' => 'کد ملی'])
                        @include('auth.registerField', ['key' => 'salary', 'label' => 'حقوق ماهیانه'])
                        @include('auth.registerField', ['key' => 'address', 'label' => 'آدرس'])
                        @include('auth.registerField', ['key' => 'phone', 'label' => 'تلفن ثابت'])
                        @include('auth.registerField', ['key' => 'mobile', 'label' => 'تلفن همراه'])

                        <div class="form-group row">
                            <label for="company_id" class="col-md-4 col-form-label text-md-right">شرکت</label>
                            <div class="col-md-6">
                                <select id="company_id" class="form-control @error('company_id') is-invalid @enderror" name="company_id" required autofocus>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('auth.Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('auth.Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth.Register') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
