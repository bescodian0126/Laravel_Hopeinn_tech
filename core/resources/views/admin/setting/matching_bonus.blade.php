@extends('admin.layouts.app')
@section('panel')
    <div class="row mt-4">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <form action="{{ route('admin.matching.bonus') }}" method="post">
                    <div class="card-body">
                        @csrf
                        <div class="row justify-content-between mb-5">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total BV')</label>
                                    <div class="input-group custom-height">
                                        <input type="number" class="form-control" value="{{ getAmount($general->total_bv) }}"
                                            name="total_bv" required>
                                        <span class="input-group-text">@lang('BV')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 mt-md-4">
                                <h2 class="text-center">=</h2>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="form-group">
                                    <label>@lang('BV Price')</label>
                                    <div class="input-group custom-height">
                                        <input type="number" class="form-control" name="bv_price"
                                            value="{{ getAmount($general->bv_price) }}" step="any" required>
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>@lang('Max BV')</label>
                                    <div class="input-group mb-3 custom-height">
                                        <input type="number" class="form-control" value="{{ getAmount($general->max_bv) }}"
                                            name="max_bv">
                                        <span class="input-group-text">@lang('BV')</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>@lang('Carry / Flush')</label>
                                    <select class="form-control" name="cary_flash" required>
                                        <option value="0">@lang('Carry (Cut Only Paid BV)')</option>
                                        <option value="1">@lang('Flush (Cut Weak Leg Value)')</option>
                                        <option value="2">@lang('Flush (Cut All BV and reset to 0)')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Matching Bonus Time')</label>
                                    <select name="matching_bonus_time" class="form-control">
                                        <option value="daily">@lang('Daily')</option>
                                        <option value="weekly">@lang('Weekly')</option>
                                        <option value="monthly">@lang('Monthly')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="daily_time" style="display:none;">
                                <div class="form-group">
                                    <label>@lang('Daily Time')</label>
                                    <select name="daily_time" class="form-control">
                                        <option value="1">@lang('01:00')</option>
                                        <option value="2">@lang('02:00')</option>
                                        <option value="3">@lang('03:00')</option>
                                        <option value="4">@lang('04:00')</option>
                                        <option value="5">@lang('05:00')</option>
                                        <option value="6">@lang('06:00')</option>
                                        <option value="7">@lang('07:00')</option>
                                        <option value="8">@lang('08:00')</option>
                                        <option value="9">@lang('09:00')</option>
                                        <option value="10">@lang('10:00')</option>
                                        <option value="11">@lang('11:00')</option>
                                        <option value="12">@lang('12:00')</option>
                                        <option value="13">@lang('13:00')</option>
                                        <option value="14">@lang('14:00')</option>
                                        <option value="15">@lang('15:00')</option>
                                        <option value="16">@lang('16:00')</option>
                                        <option value="17">@lang('17:00')</option>
                                        <option value="18">@lang('18:00')</option>
                                        <option value="19">@lang('19:00')</option>
                                        <option value="20">@lang('20:00')</option>
                                        <option value="21">@lang('21:00')</option>
                                        <option value="22">@lang('22:00')</option>
                                        <option value="23">@lang('23:00')</option>
                                        <option value="24">@lang('24:00')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4" id="weekly_time" style="display:none;">
                                <div class="form-group">
                                    <label>@lang('Weekly Time')</label>
                                    <select name="weekly_time" class="form-control">
                                        <option value="sat">@lang('Saturday')</option>
                                        <option value="sun">@lang('Sunday')</option>
                                        <option value="mon">@lang('Monday')</option>
                                        <option value="tue">@lang('Tuesday')</option>
                                        <option value="wed">@lang('Wednesday')</option>
                                        <option value="thu">@lang('Thursday')</option>
                                        <option value="fri">@lang('Friday')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="monthly_time" style="display:none;">
                                <div class="form-group">
                                    <label>@lang('Monthly Time')</label>
                                    <select name="monthly_time" class="form-control">
                                        <option value="1">@lang('1st day Month')</option>
                                        <option value="15">@lang('15th day of Month')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $("select[name=cary_flash]").val("{{ $general->cary_flash }}");
            $("select[name=matching_bonus_time]").val("{{ $general->matching_bonus_time }}");
            $("select[name=weekly_time]").val("{{ $general->matching_when }}");
            $("select[name=monthly_time]").val("{{ $general->matching_when }}");
            $("select[name=daily_time]").val("{{ $general->matching_when }}");

            $('select[name=matching_bonus_time]').on('change', function() {
                matchingBonus($(this).val());
            });

            matchingBonus($('select[name=matching_bonus_time]').val());

            function matchingBonus(matching_bonus_time) {
                if (matching_bonus_time == 'daily') {
                    document.getElementById('weekly_time').style.display = 'none';
                    document.getElementById('monthly_time').style.display = 'none'
                    document.getElementById('daily_time').style.display = 'block'

                } else if (matching_bonus_time == 'weekly') {
                    document.getElementById('weekly_time').style.display = 'block';
                    document.getElementById('monthly_time').style.display = 'none'
                    document.getElementById('daily_time').style.display = 'none'
                } else if (matching_bonus_time == 'monthly') {
                    document.getElementById('weekly_time').style.display = 'none';
                    document.getElementById('monthly_time').style.display = 'block'
                    document.getElementById('daily_time').style.display = 'none'
                }
            }

        })(jQuery);
    </script>
@endpush
