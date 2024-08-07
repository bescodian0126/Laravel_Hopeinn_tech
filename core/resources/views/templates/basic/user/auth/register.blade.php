@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $content = getContent('register.content', true);
    @endphp
    <section class="account-section padding-bottom padding-top">
        <div class="container">
            <div class="account-wrapper">
                <div class="login-area account-area">
                    <div class="row m-0">
                        <div class="col-lg-4 p-0">
                            <div class="change-catagory-area bg_img" data-background="{{ getImage('assets/images/frontend/register/' . @$content->data_values->background_image, '450x970') }}">
                                <h4 class="title">@lang('Welcome To') {{ __($general->site_name) }}</h4>
                                <p>@lang('Already have an account?')</p>
                                <a class="custom-button account-control-button" href="{{ route('user.login') }}">@lang('Login')</a>
                            </div>
                        </div>
                        <div class="col-lg-8 p-0">
                            <div class="common-form-style bg-one create-account">
                                <h4 class="title">{{ __(@$content->data_values->heading) }}</h4>
                                <p class="mb-sm-4 mb-3">{{ __(@$content->data_values->short_details) }}</p>
                                <form class="create-account-form row verify-gcaptcha" method="post" action="{{ route('user.register') }}">
                                    @csrf                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Username')</label>
                                            <input class="checkUser" name="username" type="text" value="{{ old('username') }}" required>
                                            <small class="text--danger usernameExist"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Email')</label>
                                            <input class="checkUser" name="email" type="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Country')</label>
                                            <select name="country">
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}">
                                                        {{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Mobile')</label>
                                            <div class="input-group">
                                                <span class="input-group-text mobile-code">

                                                </span>
                                                <input name="mobile_code" type="hidden">
                                                <input name="country_code" type="hidden">
                                                <input class="form-control" name="mobile" type="text" value="{{ old('mobile') }}" placeholder="@lang('Your Phone Number')" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Password')</label>
                                            <input name="password" type="password" required>
                                            @if ($general->secure_password)
                                                <div class="input-popup">
                                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                                    <p class="error number">@lang('1 number minimum')</p>
                                                    <p class="error special">@lang('1 special character minimum')</p>
                                                    <p class="error minimum">@lang('6 character password')</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Confirm password')</label>
                                            <input name="password_confirmation" type="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6" hidden>
                                            <input class="checkUser" name="refuser" type="text" value="{{ $refuser }}" required>
                                    </div>

                                    <div class="col-md-6" hidden>
                                            <input class="checkUser" name="position" type="text" value="{{ $position }}" required>
                                    </div>

                                    <div class="col-md-12">
                                        <x-captcha isCustom="true" />
                                    </div>

                               

                                    @if ($general->agree)
                                        <div class="col-md-12 mb-3">
                                            <div class="form--check">
                                                <input class="form-check-input" id="agree" name="agree" type="checkbox" @checked(old('agree')) required>
                                                <label class="form-check-label" for="agree">
                                                    @lang('I agree with')
                                                </label>
                                                <span class="ms-1">
                                                    @foreach ($policyPages as $policy)
                                                        <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </span>
                                            </div>

                                        </div>
                                    @endif
                                    <div class="form-group col-md-12">
                                        <input class="w-100" type="submit" value="@lang('Create an Account')">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="existModalCenter" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('You are with us')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="h5 text-center">@lang('You already have an account please Login ')</h5>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark h-auto w-auto" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    <a class="btn btn--base" href="{{ route('user.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .country-code .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }
    </style>
@endpush
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        "use strict";
        (function($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });

            

        })(jQuery);
    </script>
@endpush
