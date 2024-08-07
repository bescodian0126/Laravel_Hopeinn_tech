@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">{{ __($pageTitle) }}</h3>
        </div>
        @if (!count($tasks))
            <div class="row">
                <h4>No Task Launched</h4>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="d-flex justify-content-between mb-3 flex-wrap gap-1 text-end">
                    <h3 class="dashboard-title">@lang('Start your Tasks') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('The purchase will decrease your energy. When you are all right to the question, you will give energy!')"></i></h3>
                    <a class="btn btn--base btn--smd" href="{{ route('user.task.history') }}">@lang('Task Transaction History')</a>
                </div>
                <div class="col-md-12">
                    <div class="accordion table--acordion" id="transactionAccordion">
                        @forelse($tasks as $task)
                            <div class="accordion-item transaction-item">
                                <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#c-{{ $loop->iteration }}">
                                        <div class="col-lg-4 col-sm-5 col-5 order-1 icon-wrapper">
                                            <div class="left">
                                                <div class="icon tr-icon icon-success">
                                                    <i class="fas fa-tasks"></i>
                                                </div>
                                                <div class="content">
                                                    <h6 class="trans-title">{{ $task->task_name }}</h6>
                                                    <span
                                                        class="text-muted font-size--14px mt-2">{{ showDateTime($task->created_at, 'M d Y @g:i:a') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-8 col-sm-7 col-7 order-sm-3 order-2 text-end amount-wrapper">
                                            <p>
                                                <b>@lang('Required balance'): {{ showAmount($task->energy_cost) }} {{ __($general->cur_text) }}</b><br>
                                                <b class="fw-bold">@lang('Payment Reward'): {{ showAmount($task->reward) }} {{ __($general->cur_text) }}</b>
                                            </p>

                                        </div>
                                    </button>
                                </h2>
                                <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                    data-bs-parent="#transactionAccordion">
                                    <div class="accordion-body">
                                        <ul class="caption-list">
                                            <li>
                                                <span class="caption">@lang('Required Balance')</span>
                                                <span class="value">{{ showAmount($task->energy_cost) }} {{ __($general->cur_text) }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Payment Reward')</span>
                                                <span class="value">{{ showAmount($task->reward) }} {{ __($general->cur_text) }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Plan Name')</span>
                                                <span class="value">{{$task->plan_name}}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Description')</span>
                                                <span class="value">{{ $task->description }}</span>
                                            </li>
                                            <li>
                                                <button class="btn btn--base w-100 mt-1 start_task_btn"
                                                    data-task_id="{{ $task->id }}"
                                                    data-task_name="{{ $task->task_name }}"
                                                    data-cost_energy="{{ $task->energy_cost }}"
                                                    data-reward_energy="{{ $task->reward }}"
                                                    data-question_counts="{{ !empty($task->taskQuizzes) ? count($task->taskQuizzes) : 0 }}"
                                                    data-gateway_currency="{{ $gateway_currency }}" type="button">
                                                    @lang('Start')
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- task-item end -->
                        @empty
                            <div class="accordion-body text-center">
                                <h4 class="text--muted"><i class="far fa-frown"></i> {{ __(asfasdf) }}</h4>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        @endif

    </div>
    @include($activeTemplate . 'partials.task_modals')
@endsection

@push('script')
    <script>
        'use strict';
    </script>
@endpush
