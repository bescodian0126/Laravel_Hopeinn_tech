@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between mb-3 flex-wrap gap-1 text-end">
                    <h3 class="dashboard-title">@lang('Sell Energy') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('The fund will be withdrawal from your account balnace. So make sure that you\'ve sufficient balance to the account. ')"></i></h3>
                    <a class="btn btn--base btn--smd" href="{{ route('user.energy_history') }}">@lang('Sell Energy History')</a>
                </div>
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="{{ route('user.energy_shop.sell_energy_confirm') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Current Energy')</label>
                                <div class="input-group">
                                    <div class="form-control form--control">{{$formatted_energy}}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Method')</label>
                                <select class="form-select form--control" name="method" required>
                                    <option value="">@lang('Select Method')</option>
                                    <option value="{{$formatted_energy}}">@lang('Balance :') {{$formatted_energy}}</option>
                                </select>
                                <p class="limit-error text--danger"></p>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Amount')</label>
                                <div class="input-group">
                                    <input class="form-control form--control" name="amount" type="number" value="{{ old('amount') }}" step="any" required>
                                    <span class="input-group-text"><i class="fas fa-bolt"></i></span>
                                </div>
                            </div>
                            <button class="btn btn--base w-100 submitBtn mt-3" type="submit">@lang('Submit')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        
    </script>
@endpush
