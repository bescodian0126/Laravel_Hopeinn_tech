@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        $kycInfo = getContent('kyc_info.content', true);
        $notice = getContent('notice.content', true);
    @endphp
    <div class="dashboard-inner">
        @if ($user->kv == 0)
            <div class="alert border--info border" role="alert">
                <div class="alert__icon d-flex align-items-center text--info">
                    <i class="fas fa-file-signature"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Required')</span>
                    <br>
                    <small><i>{{ __($kycInfo->data_values->verification_content) }} <a class="link-color" href="{{ route('user.kyc.form') }}">@lang('Click Here to Verify')</a></i></small>
                </p>
            </div>

            <script>
                var alertList = document.querySelectorAll('.alert');
                alertList.forEach(function(alert) {
                    new bootstrap.Alert(alert)
                })
            </script>
        @elseif($user->kv == 2)
            <div class="alert border--warning border" role="alert">
                <div class="alert__icon d-flex align-items-center text--warning">
                    <i class="fas fa-user-check"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Pending')</span>
                    <br>
                    <small><i>{{ __($kycInfo->data_values->pending_content) }} <a class="link-color" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></i></small>
                </p>
            </div>
        @endif

        @if (@$notice->data_values->notice_content != null && !$user->plan_id)
            <div class="card custom--card">
                <div class="card-header">
                    <h5>@lang('Notice')</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ __($notice->data_values->notice_content) }}
                    </p>
                </div>
            </div>
        @endif

        <div class="row g-3 mt-3 mb-4">

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Deposit')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($totalDeposit) }} {{ __($general->cur_text) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($submittedDeposit) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($pendingDeposit) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($rejectedDeposit) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Widthdraw')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($totalWithdraw) }} {{ __($general->cur_text) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($submittedWithdraw) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($pendingWithdraw) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <span>{{ $general->cur_sym }}{{ showAmount($rejectWithdraw) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Referral Commission')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_ref_com) }} {{ __($general->cur_text) }}
                    </h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Referrals')</p>
                                <span>{{ $totalRef }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Left')</p>
                                <span>{{ $totalLeft }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Right')</p>
                                <span>{{ $totalRight }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Invest')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_invest) }} {{ __($general->cur_text) }}
                    </h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Binary Commission')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_binary_com) }}
                        {{ __($general->cur_text) }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ $totalBv }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Left BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_left) }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Right BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_right) }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Bv Cut')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($totalBvCut) }}</h3>
                </div>
            </div>

        </div>

        <div class="mb-4">
            <h4 class="mb-2">@lang('Binary Summery')</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table--responsive--md table">
                                <thead>
                                    <tr>
                                        <th>@lang('Paid left')</th>
                                        <th>@lang('Paid right')</th>
                                        <th>@lang('Free left')</th>
                                        <th>@lang('Free right')</th>
                                        <th>@lang('Bv left')</th>
                                        <th>@lang('Bv right')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $logs->paid_left }}</td>
                                        <td>{{ $logs->paid_right }}</td>
                                        <td>{{ $logs->free_left }}</td>
                                        <td>{{ $logs->free_right }}</td>
                                        <td>{{ getAmount($logs->bv_left) }}</td>
                                        <td>{{ getAmount($logs->bv_right) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
