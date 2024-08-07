@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <form class="notify-form" action="">
                    @csrf
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Subject') </label>
                                    <input class="form-control" name="subject" type="text" placeholder="@lang('Email subject')" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message') </label>
                                    <textarea class="form-control nicEdit" name="message" rows="10"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Start Form') </label>
                                            <input class="form-control" name="start_form" type="number" placeholder="@lang('Start form user')" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Per Batch') </label>
                                            <div class="input-group">
                                                <input class="form-control" name="batch" type="number" placeholder="@lang('How many user')" required />
                                                <span class="input-group-text">
                                                    @lang('User')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Cooling Period') </label>
                                            <div class="input-group">
                                                <input class="form-control" name="cooling_time" type="number" placeholder="@lang('Waiting time')" required />
                                                <span class="input-group-text">
                                                    @lang('Seconds')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn w-100 h-45 btn--primary me-2" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notificationSending" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Notification Sending')</h5>
                </div>
                <div class="modal-body">
                    <h4 class="text--danger dontCloseWarning text-center">@lang('Don\'t close or refresh the window till finish.')</h4>

                    <div class="mail-wrapper">
                        <div class="sendingIcon mail-icon world-icon"><i class="las la-globe"></i></div>
                        <div class="coolingIcon mail-icon world-icon"><i class="fas fa-spinner fa-spin"></i></div>
                        <div class='sendingIcon mailsent'>
                            <div class='envelope'>
                                <i class='line line1'></i>
                                <i class='line line2'></i>
                                <i class='line line3'></i>
                                <i class="icon fa fa-envelope"></i>
                            </div>
                        </div>
                        <div class="sendingIcon mail-icon mail-icon"><i class="las la-envelope-open-text"></i></div>
                    </div>
                    <div class="finalStatistics d-none">
                        <div class="mail-icon text--success fw-bold text-center">
                            <i class="fas fa-check"></i> @lang('Done')
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Start From')<span class="fw-bold startFrom">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Ended at')<span class="fw-bold sent">0</span>
                            </li>
                        </ul>
                    </div>
                    <h4 class="text--primary remainingTime d-none text-center"></h4>

                    <div class="mt-3">
                        <p class="sentStatistics text-center mb-2">@lang('Email sent') <span class="startFrom">0</span> @lang('to') <span class="sent">-</span> @lang('users')
                        </p>
                        <p class="text-center sentStatistics">
                            <button class="btn btn--danger stopSending"><i class="la la-power-off"></i>@lang('Stop')</button>
                        </p>
                        <div class="modelCloseButton d-none text-end">
                            <button class="btn btn--danger" data-bs-dismiss="modal" type="button" aria-label="Close">
                                @lang('Close')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <span class="text--primary">@lang('Notification will send via ') @if ($general->en)
            <span class="badge badge--warning">@lang('Email')</span>
            @endif @if ($general->sn)
                <span class="badge badge--warning">@lang('SMS')</span>
            @endif
    </span>
@endpush

@push('style')
    <style>
        .coolingIcon {
            margin: 0 auto;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            var subject = null,
                message = null,
                start = null,
                perBatch = null,
                sendingStatus = true,
                coolingTime = null,
                _token = null;
            $('.notify-form').on('submit', function(e) {
                subject = $(this).find('[name=subject]').val();
                message = $(this).find('.nicEdit-main').html();
                start = parseInt($(this).find('[name=start_form]').val());
                perBatch = parseInt($(this).find('[name=batch]').val());
                coolingTime = parseInt($(this).find('[name=cooling_time]').val());
                _token = $(this).find('[name=_token]').val();

                if ({{ $users }} <= 0) {
                    notify('error', 'Users not found');
                    return false;
                }
                if (!coolingTime) {
                    notify('error', `@lang('Cooling period must be greater then zero')`);
                    return false;
                }
                if (!perBatch) {
                    notify('error', `@lang('Per batch must be greater then zero')`);
                    return false;
                }
                e.preventDefault();
                sendingStatus = true;
                $('.progress-bar').css('width', `0%`);
                $('.progress-bar').text(`0%`);
                $('.sent').text('-');
                $('.stopSending,.dontCloseWarning,.sentStatistics').removeClass('d-none');
                $('.finalStatistics,.modelCloseButton').addClass('d-none');
                $('#notificationSending').modal('show');



                $('.startFrom').text(start);
                postMail();
            });

            function postMail() {
                if (!sendingStatus) {
                    $('.remainingTime,.coolingIcon,.dontCloseWarning,.sentStatistics').addClass('d-none')
                    $('.finalStatistics,.modelCloseButton').removeClass('d-none');
                    return;
                }
                $('.remainingTime').text('Cooling...')
                $('.remainingTime,.coolingIcon').addClass('d-none')
                $('.sendingIcon').removeClass('d-none')
                $.post("{{ route('admin.users.notification.all.send') }}", {
                    "subject": subject,
                    "_token": _token,
                    "start": start,
                    "batch": perBatch,
                    "message": message
                }, function(response) {
                    $('.remainingTime').removeClass('d-none')
                    $('.sendingIcon').addClass('d-none')
                    $('.coolingIcon').removeClass('d-none')
                    if (response.error) {
                        response.error.forEach(error => {
                            notify('error', error)
                        });
                    } else {
                        start += parseInt(response.total_sent);
                        $('.sent').text(start);
                        if (!parseInt(response.total_sent)) {
                            sendingStatus = false;
                            postMail();
                            return;
                        }
                        $('.sentStatistics').removeClass('d-none');
                        setTimeout(function() {
                            clearInterval(interval)
                            postMail();
                        }, coolingTime * 1000);
                        var counter = coolingTime - 1,
                            interval = setInterval(function() {
                                $('.remainingTime').text("Reloading after " + counter + " seconds");
                                counter--;
                                if (counter <= 0) clearInterval(interval);
                            }, 1000);


                    }
                });
            }

            $('.stopSending').on('click', function() {
                sendingStatus = false;
                notify('info', `@lang('Notification sending will stop after this batch.')`);
                $('.sentStatistics').addClass('d-none')
            });

        })(jQuery);
    </script>
@endpush
