@auth
    <div class="modal fade" id="purchase_task_modal">
        <div class="modal-dialog" role="dialog" style="max-width : 700px">
            <div class="modal-content">
                <form action="{{ route('user.task.start') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title m-0">@lang('Now Start task!')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <input name="amount" type="hidden" value="0">
                    <input name="task_id" type="hidden" />
                    <div class="modal-body">
                        <div class="form-group">
                            <input id="task_id_area" name="task_id" type="hidden"/>
                            <div class="form-control">
                                Task Name : <span id="task_name"></span>
                            </div>
                            <div class="form-control">
                                Required Energy : <span id="task_cost_energy"></span>
                            </div>
                            <div class="form-control">
                                Reward Energy : <span id="task_reward_energy"></span>
                            </div>
                            <div class="form-control" style="margin-bottom: 15px">
                                Question counts : <span id="task_question_counts"></span>
                            </div>

                            <div class="form-control" style="margin-bottom: 15px">
                                Balance : <span id="payment_method">{{showAmount(auth()->user()->balance)}} {{ __($general->cur_text) }}</span>
                            </div>
                            <input name = "payment_method" type="hidden" value = "{{auth()->user()->balance}}"/>
                            
                            <code class="gateway-info d-none"><span class="rate-info">@lang('Rate'):
                                    1{{ __($general->cur_text) }} = <span class="gateway-rate"></span> <span
                                        class="method_currency"></spanc>.</span> @lang('Charge'): <span class="charge">
                                        </spanc> {{ __($general->cur_text) }}. @lang('Total amount'): <span
                                            class="total"></span> {{ __($general->cur_text) }}. </code>
                            <code class="gateway-limit"></code>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--base w-100" id="submit_start_task_btn">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="modal fade" id="loginModal" role="dialog" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title m-0">@lang('Confirmation Alert!')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span class="text-center">@lang('Please login to subscribe plans.')</span>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark h-auto w-auto" data-bs-dismiss="modal"
                            type="button">@lang('Close')</button>
                        <a class="btn btn--base w-auto" href="{{ route('user.login') }}">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>

    @endauth

    @push('script')
        <script>
            'use strict';
            var routeUrl;
            var task_id;

            

            var gateway_currency = @json($gateway_currency);
            console.log(gateway_currency);

            $('.start_task_btn').on('click', function() {
                var modal = $('#purchase_task_modal');
                var ttt = 'aaa';

                modal.find('#task_name').html($(this).data('task_name'));
                modal.find('input[name="task_id"]').val($(this).data('task_id'));
                modal.find('#task_cost_energy').html($(this).data('cost_energy'));
                modal.find('#task_reward_energy').html($(this).data('reward_energy'));
                modal.find('#task_question_counts').html($(this).data('question_counts'));
                modal.modal('show');
                console.log($(this).data('task_id'));
                task_id = $(this).data('task_id');
            });

            // Replace the placeholder with the actual task ID
        </script>
    @endpush
