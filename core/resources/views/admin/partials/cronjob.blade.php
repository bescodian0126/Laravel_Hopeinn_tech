{{--   modal-- --}}
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('Cron Job Setting Instruction')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="la la-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 my-2">
                        <p class="cron-p-style"> @lang('To automate BV Matching Bonus, choose your required setting from above and run the')
                            <code> @lang('cron job') </code> @lang('on your server. Set the Cron time as minimum as possible. Once per')
                            <code>@lang('5-15')</code> @lang('minutes is ideal.')
                        </p>
                    </div>
                    <div class="col-md-12">
                        <label>@lang('Cron Command')</label>
                        <div class="input-group ">
                            <input id="ref" type="text" class="form-control form-control-lg"
                                value="curl -s {{ route('cron') }}" readonly="">
                            <button type="button" class="btn btn--success" id="copyBtn">@lang('COPY')</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--danger h-45 w-100" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    @if (Carbon\Carbon::parse($general->last_cron)->diffInSeconds() >= 900)
        <script>
            'use strict';
            (function($) {
                window.onload = () => {
                    $("#myModal").modal('show');
                }

                $("#copyBtn").click(function() {
                    var copyText = document.getElementById("ref");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999)
                    document.execCommand("copy");
                    notify('success', 'Url copied successfully ' + copyText.value);
                });

            })(jQuery)
        </script>
    @endif
@endpush
