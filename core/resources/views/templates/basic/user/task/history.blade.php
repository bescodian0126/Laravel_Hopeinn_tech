@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <div class="d-flex justify-content-between">
                <h3 class="mb-2">@lang('Task Transaction History')</h3>
                <span>
                    <a href="{{ route('user.task.index') }}" class="btn btn--base btn--smd">@lang('Purchase Task Now')</a>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">

                <div class="accordion table--acordion" id="transactionAccordion">
                    @forelse($task_transactions as $task_trans)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}" aria-expanded="false" aria-controls="c-1">
                                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            @if ($task_trans->status == Status::TASK_PURCHASED)
                                                <div class="icon icon-danger">
                                                    <i class="las la-long-arrow-alt-right"></i>
                                                </div>
                                            @elseif ($task_trans->status == Status::TASK_SUCCESS)
                                                <div class="icon icon-success">
                                                    <i class="las la-check"></i>
                                                </div>
                                            @elseif($task_trans->status == Status::TASK_PENDING)
                                                <div class="icon icon-warning">
                                                    <i class="las la-spinner fa-spin"></i>
                                                </div>
                                            @elseif($task_trans->status == Status::TASK_REJECT)
                                                <div class="icon icon-danger">
                                                    <i class="las la-ban"></i>
                                                </div>
                                            @elseif($task_trans->status == Status::TASK_GET_BONUS)
                                                <div class="icon icon-success">
                                                    <i class="las la-ban"></i>
                                                </div>
                                            @endif
                                            <div class="content">
                                                <h6 class="trans-title">{{ __($task_trans->remark) }}</h6>
                                                <span class="text-muted font-size--14px mt-2">
                                                    {{ showDateTime($task_trans->created_at, 'M d Y @g:i:a') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted font-size--14px"><b>{{ $task_trans->task->task_name }}</b></p>
                                    </div>
                                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        <p><b>{{ showAmount($task_trans->amount) }} {{ __($general->cur_text) }}</b></p>
                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Amount')</span>
                                            <span class="value">{{ showAmount($task_trans->amount) }}
                                                {{ __($general->cur_text) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('After Transaction')</span>
                                            <span class="value">{{ showAmount($task_trans->after_energy) }}
                                                {{ __($general->cur_text) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Before Transaction')</span>
                                            <span
                                                class="value">{{ $general->cur_sym }}{{ showAmount($task_trans->before_energy) }}
                                                {{-- x {{ $general->cur_sym }}{{ showAmount($task->rate) }} =
                                                {{ $general->cur_sym }}{{ showAmount($withdraw->final_amount) }} --}}
                                            </span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Status')</span>
                                            <span class="value">
                                                @php echo $task_trans->statusBadge @endphp <button type="button" class="btn p-0"><i
                                                        class="las la-info-circle detailBtn"
                                                        data-user_data="{{ json_encode($task_trans->details) }}"
                                                        @if ($task_trans->status == Status::TASK_REJECT) data-admin_feedback="{{ $task_trans->details }}" @endif></i></button>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- transaction-item end -->
                    @empty
                        <div class="accordion-body bg-white text-center">
                            <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                        </div>
                    @endforelse
                </div>


            </div>
        </div>
    </div>



    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData">

                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var history = @json($task_transactions);
            console.log(history);

        })(jQuery);
    </script>
@endpush
