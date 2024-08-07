@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="accordion table--acordion" id="transactionAccordion">
                @forelse($energy_history as $e_log)
                    <div class="accordion-item transaction-item">
                        <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#c-{{ $loop->iteration }}">
                                <div class="col-lg-4 col-sm-5 col-5 order-1 icon-wrapper">
                                    <div class="left">
                                        <div
                                            class="icon tr-icon @if ($e_log->type == '+') icon-success @else icon-danger @endif">
                                            <i class="las la-long-arrow-alt-right"></i>
                                        </div>
                                        <div class="content">
                                            <h6 class="trans-title">{{ __(keyToTitle($e_log->remark)) }}</h6>
                                            <span class="text-muted font-size--14px mt-2">{{
                                                showDateTime($e_log->created_at, 'M d Y @g:i:a') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8 col-sm-7 col-7 order-sm-3 order-2 text-end amount-wrapper">
                                    <p>
                                        <b>@lang('Current energy'): <i
                                                class="fas fa-bolt"></i>{{ showAmount($e_log->current_energy) }}</b><br>
                                        <b class="fw-bold">@lang('Past Energy'): <i
                                                class="fas fa-bolt"></i>{{ showAmount($e_log->past_energy) }}</b>
                                    </p>

                                </div>
                            </button>
                        </h2>
                        <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                            data-bs-parent="#transactionAccordion">
                            <div class="accordion-body">
                                <ul class="caption-list">
                                    <li>
                                        <span class="caption">@lang('Amount')</span>
                                        <span class="value">{{ $e_log->amount }}</span>
                                    </li>
                                    <li>
                                        <span class="caption">@lang('Current Energy')</span>
                                        <span class="value"><i
                                                class="fas fa-bolt"></i>&nbsp;{{ showAmount($e_log->current_energy) }}</span>
                                    </li>
                                    <li>
                                        <span class="caption">@lang('Past Energy')</span>
                                        <span class="value"><i
                                                class="fas fa-bolt"></i>&nbsp;{{ showAmount($e_log->past_energy) }}</span>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div><!-- task-item end -->
                @empty
                    <div class="accordion-body text-center">
                        <h4 class="text--muted"><i class="far fa-frown"></i> There is no history</h4>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection

@push('script')
    <script>
        'use strict';
        var tasks = @json($energy_history);
        console.log(tasks);

    </script>
@endpush