@php
    $breadcrumb = getContent('breadcrumb.content', true);
@endphp
<section class="page-header bg_img"
    data-background="{{ getImage('assets/images/frontend/breadcrumb/' . @$breadcrumb->data_values->background_image, '1920x250') }}">
    <div class="container">
        <div class="page-header-wrapper">
            <h2 class="title">{{ __($pageTitle) }}</h2>
            <ul class="breadcrumb">
                <li>
                    <a href="{{ url('/') }}">
                        @lang('Home')
                    </a>
                </li>
                <li>{{ __($pageTitle) }}</li>
            </ul>
        </div>
    </div>
</section>
