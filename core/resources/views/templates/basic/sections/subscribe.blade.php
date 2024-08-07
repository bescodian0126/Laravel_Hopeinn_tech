@php
    $subscribe = getContent('subscribe.content', true);
@endphp
<section class="subscribe-section padding-top padding-bottom bg-overlay bg_img bg_fixed" id="subscribe"
    data-background="{{ getImage('assets/images/frontend/subscribe/' . @$subscribe->data_values->background_image, '1920x475') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="subscribe-content">
                    <h4 class="title">{{ __(@$subscribe->data_values->heading) }}</h4>
                    <form class="subscribe-form" method="post" action="{{ route('subscribe') }}">
                        @csrf
                        <input name="email" type="email" id="email" placeholder="@lang('Enter Your email address')" required>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        'use strict';
        (function($) {
            $(document).on('submit', '.subscribe-form', function(e) {
                e.preventDefault();
                var email = $("#email").val();
                if (email) {
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        url: "{{ route('subscribe') }}",
                        method: "POST",
                        data: {
                            email: email
                        },
                        success: function(response) {
                            if (response.success) {
                                notify('success', response.success);
                                $("#email").val('');
                            } else {
                                $.each(response.error, function(i, val) {
                                    notify('error', val);
                                });
                            }
                        }
                    });
                } else {
                    notify('error', "Please Input Your Email");
                }
            });

        })(jQuery);
    </script>
@endpush
