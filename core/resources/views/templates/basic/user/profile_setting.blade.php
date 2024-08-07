@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">@lang('Profile')</h3>
    </div>
    <div class="card custom--card">
        <div class="card-body">
            <form class="register" action="" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group">
                        <label class="form-control-label">@lang('Username')</label>
                        <input class="form-control form--control" id="username" name="username" type="text"
                            value="{{ $user->username }}" required>
                        <div id="username_error" class="validation-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('First Name')</label>
                        <input type="text" class="form-control form--control" name="firstname"
                            value="{{ $user->firstname }}" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Last Name')</label>
                        <input type="text" class="form-control form--control" name="lastname"
                            value="{{ $user->lastname }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('E-mail Address')</label>
                        <input class="form-control form--control" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Mobile Number')</label>
                        <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Address')</label>
                        <input type="text" class="form-control form--control" name="address"
                            value="{{ @$user->address->address }}">
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('State')</label>
                        <input type="text" class="form-control form--control" name="state"
                            value="{{ @$user->address->state }}">
                    </div>
                </div>


                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('Zip Code')</label>
                        <input type="text" class="form-control form--control" name="zip"
                            value="{{ @$user->address->zip }}">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('City')</label>
                        <input type="text" class="form-control form--control" name="city"
                            value="{{ @$user->address->city }}">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-label">@lang('Country')</label>
                        <input class="form-control form--control" value="{{ @$user->address->country }}" disabled>
                    </div>

                </div>

                <div class="form-group">
                    <label for="image" class="form-label">@lang('Profile Picture')</label>
                    <input type="file" name="image" id="image" class="form-control form--control"
                        accept=".png, .jpg, .jpeg">

                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>

    </div>
    @endsection

    @push('style')
        <style>
            .validation-message {
                color: red;
                font-size: 0.875em;
                margin-top: 5px;
            }
        </style>
    @endpush

    @push('script')
        <script>
            (function ($) {
                'use strict'
                //SSS Change Username
                var user = @json($user);
                console.log(user);
                $('#username').on('keyup', function () {
                    var username = $(this).val();
                    $.get(
                        "{{route('admin.users.check_username')}}",
                        { 'username': username, 'id' : user['id']},
                        function (response) {
                            console.log(response);
                            if (response.message == 'user not found') {
                                $('#username_error').text('This username is avaliable');
                                $('#username_error').css('color', 'green');
                                $('#username_error').show();
                            } else if(response.message == 'current user'){
                                $('#username_error').text('This is current username');
                                $('#username_error').css('color', 'green');
                                $('#username_error').show();
                            }else {
                                $('#username_error').text('This username is already exists');
                                $('#username_error').css('color', 'red');
                                $('#username_error').show();
                            }
                        }
                    )
                })
            })(jQuery)
        </script>
    @endpush