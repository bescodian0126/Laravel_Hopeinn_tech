@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account-section padding-bottom padding-top">
        <div class="container">
            <div class="bg-one login-account">
                <div class="row justify-content-center m-0">
                    <div class="col-lg-6">
                        <div class="common-form-style bg-one login-account account-wrapper">
                            <form class="create-account-form verify-gcaptcha" method="post" action="{{ route('user.password.email') }}">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Email or Username')</label>
                                    <input name="value" type="text" value="{{ old('value') }}" required autofocus="off">
                                </div>
                                <x-captcha />
                                <div class="form-group">
                                    <input class="w-100" type="submit" value="@lang('Submit')">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
