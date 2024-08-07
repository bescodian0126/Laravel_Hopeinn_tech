@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-4">
                    <h3 class="dashboard-title">@lang('Balance Transfer') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('You can transfer the balance to another user from your account balance. The transferred amount will be added to the account blance of the targeted user.')"></i></h3>
                </div>
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Username')</label>
                                <input class="form--control form-control" name="username" type="text" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label>
                                    @lang('Amount') <small class="text--base">(@lang('Charge'):
                                        {{ showAmount($general->balance_transfer_fixed_charge) }} {{ __($general->cur_text) }} +
                                        {{ getAmount($general->balance_transfer_per_charge) }}%
                                        @if ($general->balance_transfer_min != 0)
                                            @lang('| Min Limit'): {{ showAmount($general->balance_transfer_min) }} {{ __($general->cur_text) }}
                                        @endif
                                        @if ($general->balance_transfer_max != 0)
                                            @lang('| Max Limit'): {{ showAmount($general->balance_transfer_max) }} {{ __($general->cur_text) }}
                                        @endif
                                        )
                                    </small>
                                </label>
                                <input class="form--control form-control" name="amount" type="text" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('After Charge')</label>
                                <input class="form--control form-control after-charge" type="text" readonly autocomplete="off" required>
                            </div>
                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label>@lang('Google Authenticator Code')</label>
                                    <input class="form-control form--control" name="authenticator_code" type="text" required>
                                </div>
                            @endif
                            <div class="form-group">
                                <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict"
            $('[name=amount]').on('input', function() {
                var amount = parseFloat($(this).val());
                if (!amount) {
                    $('.after-charge').val('');
                    return false;
                }
                var percent = {{ getAmount($general->balance_transfer_per_charge) }};
                var fixed = {{ showAmount($general->balance_transfer_fixed_charge) }};
                var charge = (amount * percent / 100) + fixed;
                var withCharge = amount + charge;
                $('.after-charge').val(withCharge.toFixed(2));
            })
        })(jQuery);
    </script>
@endpush
