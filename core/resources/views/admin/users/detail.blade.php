@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-money-bill-wave-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</h3>
                        <p class="text-white">@lang('Balance')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.report.transaction') }}?search={{ $user->username }}&exact_match=true">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</h3>
                        <p class="text-white">@lang('Deposits')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.deposit.list') }}?search={{ $user->username }}">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($totalWithdrawals) }}</h3>
                        <p class="text-white">@lang('Withdrawals')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.withdraw.log') }}?search={{ $user->username }}">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--17">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-exchange-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $totalTransaction }}</h3>
                        <p class="text-white">@lang('Transactions')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.report.transaction') }}?search={{ $user->username }}&exact_match=true">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

        </div>

        <div class="row gy-4 mt-2">

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--17">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-money-bill-wave-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($user->total_invest) }}</h3>
                        <p class="text-white">@lang('Total Invest')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.report.invest') }}?search={{ $user->username }}&exact_match=true">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--18">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($user->total_ref_com) }}</h3>
                        <p class="text-white">@lang('Total Referral Commission')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.report.referral.commission') }}?search={{ $user->username }}&exact_match=true">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--3">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($user->total_binary_com) }}</h3>
                        <p class="text-white">@lang('Total Binary Commission')</p>
                    </div>
                    <a class="widget-two__btn"
                        href="{{ route('admin.report.binary.commission') }}?search={{ $user->username }}&exact_match=true">@lang('View All')</a>
                </div>
            </div>
            <!-- dashboard-w1 end -->

            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--4">
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-exchange-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ getAmount($totalBv) }}</h3>
                        <p class="text-white">@lang('Total BV')</p>
                    </div>

                </div>
            </div>
            <!-- dashboard-w1 end -->

        </div>

        <div class="d-flex mt-4 flex-wrap gap-3">
            <div class="flex-fill">
                <button class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-bs-toggle="modal"
                    data-bs-target="#addSubModal" data-act="add">
                    <i class="las la-plus-circle"></i> @lang('Balance')
                </button>
            </div>

            <div class="flex-fill">
                <button class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-bs-toggle="modal"
                    data-bs-target="#addSubModal" data-act="sub">
                    <i class="las la-minus-circle"></i> @lang('Balance')
                </button>
            </div>

            <div class="flex-fill">
                <a class="btn btn--primary btn--shadow w-100 btn-lg"
                    href="{{ route('admin.report.login.history') }}?search={{ $user->username }}">
                    <i class="las la-list-alt"></i>@lang('Logins')
                </a>
            </div>

            <div class="flex-fill">
                <a class="btn btn--secondary btn--shadow w-100 btn-lg"
                    href="{{ route('admin.users.notification.log', $user->id) }}">
                    <i class="las la-bell"></i>@lang('Notifications')
                </a>
            </div>

            <div class="flex-fill">
                <a class="btn btn--primary btn--gradi btn--shadow w-100 btn-lg"
                    href="{{ route('admin.users.login', $user->id) }}" target="_blank">
                    <i class="las la-sign-in-alt"></i>@lang('Login as User')
                </a>
            </div>

            <!-- <div class="flex-fill">
                <a class="btn btn--success btn--gradi btn--shadow w-100 btn-lg"
                    href="{{ route('admin.users.binary.tree', $user->username) }}">
                    <i class="las la-tree"></i>@lang('User Tree')
                </a>
            </div> -->

            <div class="flex-fill">
                <button class="btn btn--danger btn--gradi btn--shadow w-100 btn-lg" data-bs-toggle="modal"
                    data-bs-target="#remove_user_from_plan_modal" type="button">
                    <i class="las la-trash"></i>@lang('Delete User from Matrix')
                </button>
            </div>

            @if ($user->kyc_data)
                <div class="flex-fill">
                    <a class="btn btn--dark btn--shadow w-100 btn-lg"
                        href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank">
                        <i class="las la-user-check"></i>@lang('KYC Data')
                    </a>
                </div>
            @endif



            <div class="flex-fill">
                @if ($user->status == Status::USER_ACTIVE)
                    <button class="btn btn--warning btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal"
                        data-bs-target="#userStatusModal" type="button">
                        <i class="las la-ban"></i>@lang('Ban User')
                    </button>
                @else
                    <button class="btn btn--success btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal"
                        data-bs-target="#userStatusModal" type="button">
                        <i class="las la-undo"></i>@lang('Unban User')
                    </button>
                @endif
            </div>

            <div class="flex-fill">
                <button class="btn btn--danger btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal"
                    data-bs-target="#delete_confirmation_modal" type="button">
                    <i class="las la-trash"></i>@lang('Delete User')
                </button>
            </div>
        </div>

        <div class="card mt-30">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Information of') {{ $user->fullname }}
                    @if ($user->plan_id)
                        <span class="badge badge--success">@lang('Paid User')</span>
                    @else
                        <span class="badge badge--danger">@lang('Free User')</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('Indicator')</label>
                                <input class="form-control" id="ref_user" name="ref_user" type="text"
                                    value="{{ $user->ref_user }}" required />
                                <div id="user_list"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('Position')</label>
                                <select class="form-control" id="user_position" name="position" type="text">
                                    <option value=0>Select One</option>
                                    <option value=1>Left</option>
                                    <option value=2>Center</option>
                                    <option value=3>Right</option>
                                </select>
                                <div id="user_list"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">@lang('Username')</label>
                                <input class="form-control" id="username" name="username" type="text"
                                    value="{{ $user->username }}" required>
                                <div id="username_error" class="validation-message"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('First Name')</label>
                                <input class="form-control" name="firstname" type="text" value="{{ $user->firstname }}"
                                    required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">@lang('Last Name')</label>
                                <input class="form-control" name="lastname" type="text" value="{{ $user->lastname }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Email') </label>
                                <input class="form-control" name="email" type="email" value="{{ $user->email }}"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Mobile Number') </label>
                                <div class="input-group">
                                    <span class="input-group-text mobile-code"></span>
                                    <input class="form-control checkUser" id="mobile" name="mobile" type="number"
                                        value="{{ old('mobile') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Password')</label>
                                <input class="form-control" name="password" id="password" type="password" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Confirm Password')</label>
                                <input class="form-control" name="password_confirmation" id="password_confirmation"
                                    type="password" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="changePasswordCheckbox"
                            name="password_checkbox" value="1">
                        <input type="hidden" name="password_checkbox" value="0">
                        <label class="form-check-label" for="changePasswordCheckbox">@lang('Change Password')</label>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Address')</label>
                                <input class="form-control" name="address" type="text"
                                    value="{{ @$user->address->address }}">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label>@lang('City')</label>
                                <input class="form-control" name="city" type="text" value="{{ @$user->address->city }}">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label>@lang('State')</label>
                                <input class="form-control" name="state" type="text"
                                    value="{{ @$user->address->state }}">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label>@lang('Zip/Postal')</label>
                                <input class="form-control" name="zip" type="text" value="{{ @$user->address->zip }}">
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label>@lang('Country')</label>
                                <select class="form-control" name="country">
                                    @foreach ($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">
                                            {{ __($country->country) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-xl-3 col-md-6 col-12">
                            <label>@lang('Email Verification')</label>
                            <input name="ev" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')"
                                type="checkbox" @if ($user->ev) checked @endif>

                        </div>

                        <div class="form-group col-xl-3 col-md-6 col-12">
                            <label>@lang('Mobile Verification')</label>
                            <input name="sv" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')"
                                type="checkbox" @if ($user->sv) checked @endif>

                        </div>
                        <div class="form-group col-xl-3 col-md- col-12">
                            <label>@lang('2FA Verification') </label>
                            <input name="ts" data-width="100%" data-height="50" data-onstyle="-success"
                                data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')"
                                data-off="@lang('Disable')" type="checkbox" @if ($user->ts) checked @endif>
                        </div>
                        <div class="form-group col-xl-3 col-md- col-12">
                            <label>@lang('KYC') </label>
                            <input name="kv" data-width="100%" data-height="50" data-onstyle="-success"
                                data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                data-off="@lang('Unverified')" type="checkbox" @if ($user->kv == 1) checked @endif>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Add Sub Balance MODAL --}}
<div class="modal fade" id="addSubModal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.add.sub.balance', $user->id) }}" method="POST">
                @csrf
                <input name="act" type="hidden">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Amount')</label>
                        <div class="input-group">
                            <input class="form-control" name="amount" type="number" step="any"
                                placeholder="@lang('Please provide positive amount')" required>
                            <div class="input-group-text">{{ __($general->cur_text) }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('Remark')</label>
                        <textarea class="form-control" name="remark" placeholder="@lang('Remark')" rows="4"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="userStatusModal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($user->status == Status::USER_ACTIVE)
                        <span>@lang('Ban User')</span>
                    @else
                        <span>@lang('Unban User')</span>
                    @endif
                </h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($user->status == Status::USER_ACTIVE)
                        <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                    @else
                        <p><span>@lang('Ban reason was'):</span></p>
                        <p>{{ $user->ban_reason }}</p>
                        <h4 class="mt-3 text-center">@lang('Are you sure to unban this user?')</h4>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($user->status == Status::USER_ACTIVE)
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    @else
                        <button class="btn btn--dark" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--primary" type="submit">@lang('Yes')</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete User from Plan --}}
<div class="modal fade" id="remove_user_from_plan_modal" role="dialog" tabindex="1">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Remove user from plan')</h5>

                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>

                <div class="modal-body">
                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="table-responsive--md table-responsive mb-2">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="plans_area">
                                @foreach($purchase_plans as $plan)
                                    <tr>
                                        <td>{{$plan->name}}</td>
                                        <td>
                                            <button class="btn btn-outline--danger remove_user_confirm_btn" data-plan_name="{{$plan->name}}" data-plan_id="{{$plan->id}}" data-user_id="{{$user->id}}" data-username="{{$user->username}}">
                                                <i class="fas fa-trash" style="margin-right:0px;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn--success w-100 h-45" type="submit" data-bs-dismiss="modal"
                        id="delete_user_from_plan_btn">@lang('Save')</button>
                </div>
        </div>
    </div>
</div>

<!-- Delete user confirmation modal -->
<div class="modal fade" id="delete_confirmation_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span>@lang('Delete User')</span>
                </h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h4 class="mt-3 text-center text-danger">@lang('Are you sure to delete this user?')</h4>
                </div>
                <div class="modal-footer">
                    @if ($user->status == Status::USER_ACTIVE)
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    @else
                        <button class="btn btn--dark" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--primary" type="submit">@lang('Yes')</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove confirmation modal -->
<div class="modal fade" id="remove_user_from_plan_confirm_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span id="remove_user_confirm_title_area"></span>
                </h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.users.remove_from_plan', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <input id="remove_plan_id_area" name="remove_plan_id" type="hidden"/>
                    <h4 class="mt-3 text-center text-danger" id="remove_user_content_area"></h4>
                </div>
                <div class="modal-footer">
                    @if ($user->status == Status::USER_ACTIVE)
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    @else
                        <button class="btn btn--dark" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--primary" type="submit">@lang('Yes')</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style')
    <style>
        .form-group {
            position: relative;
            /* Ensure the relative positioning of the container */
        }

        #user_list {
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            background-color: #fff;
            z-index: 1000;
            position: absolute;
            width: calc(100% - 2px);
            /* Adjust the width to fit within the input field's border */
            top: calc(100% + 2px);
            /* Position the dropdown just below the input field */
            left: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Optional: Add some shadow for better visibility */
            display: none;
            /* Hide by default */
        }

        .validation-message {
            color: red;
            font-size: 0.875em;
            margin-top: 5px;
        }

        .list-group-item {
            padding: 8px;
            cursor: pointer;
        }

        .list-group-item:hover {
            background-color: #f0f0f0;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict"


            var user = @json($user);
            var purchase_plans = @json($purchase_plans);
            console.log(purchase_plans);

            $('#user_position').val(user['position']);

            //SSS change Ref_user
            $('#ref_user').on('keyup', function () {
                var query = $(this).val();
                if (query.length > 2) {
                    $.get(
                        "{{route('admin.users.search')}}",
                        { 'query': query },
                        function (response) {
                            var user_list = $('#user_list');
                            user_list.empty();
                            var data = response.data;
                            if (data.length > 0) {
                                data.forEach(function (user) {
                                    user_list.append('<div class="list-group-item">' + user.username + '</div>');
                                });
                                user_list.show();
                            } else {
                                user_list.append('<div class="list-group-item">No users found</div>');
                                user_list.show();
                            }
                            // console.log(response);
                        }
                    )
                } else {
                    $('#user_list').empty();
                }
            })

            $('#user_list').on('click', '.list-group-item', function () {
                $('#ref_user').val($(this).text());
                $('#user_list').empty().hide();
            });


            //SSS Change Username
            $('#username').on('keyup', function () {
                var username = $(this).val();
                $.get(
                    "{{route('admin.users.check_username')}}",
                    { 'username': username, 'id': user['id'] },
                    function (response) {
                        console.log(response);
                        if (response.message == 'user not found') {
                            $('#username_error').text('This username is avaliable');
                            $('#username_error').css('color', 'green');
                            $('#username_error').show();
                        } else if (response.message == 'current user') {
                            $('#username_error').text('This is current username');
                            $('#username_error').css('color', 'green');
                            $('#username_error').show();
                        } else {
                            $('#username_error').text('This username is already exists');
                            $('#username_error').css('color', 'red');
                            $('#username_error').show();
                        }
                    }
                )
            })

            //SSS Change password dicission
            $('#password').prop('disabled', true);
            $('#password_confirmation').prop('disabled', true);

            // Enable/disable password fields based on checkbox state
            $('#changePasswordCheckbox').change(function () {
                if ($(this).is(':checked')) {
                    $('#password').prop('disabled', false).attr('required', 'required');
                    $('#password_confirmation').prop('disabled', false).attr('required', 'required');
                } else {
                    $('#password').prop('disabled', true).removeAttr('required');
                    $('#password_confirmation').prop('disabled', true).removeAttr('required');
                }
            });

            //SSS remove user from plan confirmation
            $('.remove_user_confirm_btn').on('click', function(e){
                e.preventDefault();
                var modal = $('#remove_user_from_plan_confirm_modal');
                var plan_name = $(this).data('plan_name');
                var plan_id = $(this).data('plan_id');
                var username = $(this).data('username');
                modal.find('#remove_user_confirm_title_area').text(`Remove ${username} from ${plan_name}`);
                modal.find('#remove_user_content_area').text(`Are you sure to remove this user from ${plan_name}?`);
                modal.find('#remove_plan_id_area').val(plan_id);
                modal.modal('show');
            })

            $('.bal-btn').click(function () {
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });
            let mobileElement = $('.mobile-code');
            $('select[name=country]').change(function () {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            $('select[name=country]').val('{{ @$user->country_code }}');
            let dialCode = $('select[name=country] :selected').data('mobile_code');
            let mobileNumber = `{{ $user->mobile }}`;
            mobileNumber = mobileNumber.replace(dialCode, '');
            $('input[name=mobile]').val(mobileNumber);
            mobileElement.text(`+${dialCode}`);

        })(jQuery);
    </script>
@endpush
