@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            @if (request()->routeIs('user.recharge.log'))
                <div class="d-flex justify-content-between">
                    <h3 class="mb-2">@lang('Recharge History')</h3>
                    <span>
                        <a href="{{ route('user.epin.recharge') }}" class="btn btn--base btn--smd">
                            @lang('Recharge Now')
                        </a>
                    </span>
                </div>
            @else
                <h3 class="mb-2">@lang('Transactions')</h3>
            @endif
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (!request()->routeIs('user.recharge.log'))
                    <div class="show-filter mb-3 text-end">
                        <button type="button" class="btn btn--base showFilterBtn btn-sm"><i class="las la-filter"></i>
                            @lang('Filter')</button>
                    </div>
                    <div class="card responsive-filter-card mb-4">
                        <div class="card-body">
                            <form action="">
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="flex-grow-1">
                                        <label>@lang('Transaction Number')</label>
                                        <input type="text" name="search" value="{{ request()->search }}"
                                            class="form-control form--control">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label>@lang('Type')</label>
                                        <select name="trx_type" class="form-select form--control">
                                            <option value="">@lang('All')</option>
                                            <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                            <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label>@lang('Remark')</label>
                                        <select class="form-select form--control" name="remark">
                                            <option value="">@lang('Any')</option>
                                            @foreach ($remarks as $remark)
                                                <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                                    {{ __(keyToTitle($remark->remark)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-grow-1 align-self-end">
                                        <button class="btn btn--base w-100"><i class="las la-filter"></i>
                                            @lang('Filter')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="accordion table--acordion" id="transactionAccordion">
                    @forelse($transactions as $transaction)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}">
                                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            <div
                                                class="icon tr-icon @if ($transaction->trx_type == '+') icon-success @else icon-danger @endif">
                                                <i class="las la-long-arrow-alt-right"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="trans-title">{{ __(keyToTitle($transaction->remark)) }}</h6>
                                                <span
                                                    class="text-muted font-size--14px mt-2">{{ showDateTime($transaction->created_at, 'M d Y @g:i:a') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted font-size--14px"><b>#{{ $transaction->trx }}</b></p>
                                    </div>
                                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        <p>
                                            <b>{{ showAmount($transaction->amount) }} {{ __($general->cur_text) }}</b><br>
                                            <small class="fw-bold text-muted">@lang('Balance'):
                                                {{ showAmount($transaction->post_balance) }}
                                                {{ __($general->cur_text) }}</small>
                                        </p>

                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Charge')</span>
                                            <span class="value">{{ showAmount($transaction->charge) }}
                                                {{ __($general->cur_text) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Post Balance')</span>
                                            <span class="value">{{ showAmount($transaction->post_balance) }}
                                                {{ __($general->cur_text) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Details')</span>
                                            <span class="value">{{ __($transaction->details) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- transaction-item end -->
                    @empty
                        <div class="accordion-body text-center">
                            <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                        </div>
                    @endforelse
                </div>

                @if ($transactions->hasPages())
                    <div class="mt-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
