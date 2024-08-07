@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('login.content', true);
    @endphp
    <section class="account-section padding-bottom padding-top">
        <div class="container">
            <div class="account-wrapper">
                <div class="signup-area account-area">
                    <div class="row m-0 flex-wrap-reverse">
                        <div class="col-lg-6 p-0">
                            <div class="change-catagory-area bg_img" data-background="{{ getImage('assets/images/frontend/login/' . @$content->data_values->background_image, '650x600') }}">
                                <h4 class="title">@lang('Welcome To') {{ __($general->site_name) }}</h4>
                                <p>@lang('Haven\'t registered yet! don\'t worry just fillip all the information below and get your account now.')</p>
                                <a class="custom-button account-control-button" href="{{ route('user.register') }}">@lang('Register Now')</a>
                            </div>
                        </div>
                        <div class="col-lg-6 p-0">
                            <div class="common-form-style bg-one login-account">
                                <h4 class="title">{{ __(@$content->data_values->heading) }}</h4>
                                <p class="mb-sm-4 mb-3">{{ __(@$content->data_values->short_details) }}</p>
                                <form class="create-account-form verify-gcaptcha" method="post" action="{{ route('user.login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">@lang('Email or Username')</label>
                                        <input name="username" type="text" value="{{ old('username') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">@lang('Password')</label>
                                        <input id="myInputThree" name="password" type="password" required>
                                    </div>

                                    <x-captcha isCustom="true" />

                                    <div class="form-group d-flex justify-content-between flex-wrap">
                                        <div class="form--check">
                                            <input class="form-check-input" id="remember" name="remember" type="checkbox">
                                            <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                                        </div>
                                        <a class="text--base" href="{{ route('user.password.request') }}">@lang('Forget Password?')</a>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input class="w-100 mt-0" type="submit" value="@lang('Login')">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
