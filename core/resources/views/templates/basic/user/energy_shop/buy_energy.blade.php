@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-1 text-end">
                <h3 class="dashboard-title">@lang('Buy Energy') <i class="fas fa-question-circle text-muted text--small"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="@lang('Add funds using our system\'s balance. The deposited amount will be credited to the account energy.')"></i>
                </h3>
                <a class="btn btn--base btn--smd"
                    href="{{ route('user.energy_history') }}">@lang('Buy Energy History')</a>
            </div>
            <form action="{{route('user.energy_shop.charge_energy')}}" method="post">
                @csrf
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">@lang('Select Method')</label>
                            <select class="form--control form-select" name="user_balance" required>
                                <option value="">@lang('Select One')</option>
                                <option value="{{$formatted_balance}}">Balance : {{$formatted_balance}}</option>
                            </select>
                            <p class="text--danger limit-error"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input class="form-control form--control" name="energy_amount" id="energy_amount"
                                    type="number" value="{{ old('amount') }}" step="any" autocomplete="off" required>
                                <span class="input-group-text"><i class="fas fa-bolt"></i></span>
                            </div>
                        </div>
                        
                        <button class="btn btn--base w-100 submitBtn mt-3"
                            id="buy_energy_submit_btn">@lang('Buy')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        "use strict";

    </script>
@endpush