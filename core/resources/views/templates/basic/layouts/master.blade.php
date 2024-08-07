<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet" />

    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/animate.css') }}" rel="stylesheet">
    <!-- Plugin Link -->
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/slick.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/apexcharts.css') }}" rel="stylesheet">

    <!-- Main css -->
    <link href="{{ asset($activeTemplateTrue . 'users/css/main.css') }}" rel="stylesheet">

    @stack('style-lib')

    <link href="{{ asset($activeTemplateTrue . 'users/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/color.php') }}?color={{ $general->base_color }}" rel="stylesheet">

    @stack('style')
    <style>
        .pb-120 {
            padding-bottom: clamp(40px, 4vw, 40px);
        }

        .pt-120 {
            padding-top: clamp(40px, 4vw, 40px);
        }

        .container {
            max-width: 1140px;
        }
    </style>

</head>

<body>
    <div class="d-flex flex-wrap">
        @include($activeTemplate . 'partials.sidebar')
        <div class="dashboard-wrapper">
            @include($activeTemplate . 'partials.topbar')
            <div class="dashboard-container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Pluglin Link -->
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/magnific-popup.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/apexcharts.min.js') }}"></script>

    @stack('script-lib')

    <!-- Main js -->
    <script src="{{ asset($activeTemplateTrue . 'users/js/main.js') }}"></script>

    @stack('script')

    @include('partials.plugins')

    @include('partials.notify')

    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            var inputElements = $('input,select');
            $.each(inputElements, function(index, element) {
                element = $(element);
                var type = element.attr('type');
                if (type != 'checkbox') {
                    element.closest('.form-group').find('label').attr('for', element.attr('name'));
                    element.attr('id', element.attr('name'))
                }
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            $.each($('input, select, textarea'), function(i, element) {

                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }

            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

        })(jQuery);
    </script>
</body>

</html>
