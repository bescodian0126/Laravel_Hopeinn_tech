@php
    $banners = getContent('banner.element');
@endphp
<section class="banner-slider">
    <div class="swiper-wrapper">
        @foreach ($banners as $banner)
            <div class="swiper-slide">
                <div class="banner-container bg-overlay bg_img" data-background="{{ getImage('assets/images/frontend/banner/' . @$banner->data_values->background_image, '1920x850') }}">
                    <div class="container">
                        <div class="banner-content">
                            <h3 class="sub-title">{{ __(@$banner->data_values->title) }}</h3>
                            <h1 class="title">{{ __(@$banner->data_values->subtitle) }}</h1>
                            <div class="button-area">
                                <p>{{ __(@$banner->data_values->description) }}</p>
                                <div class="button-group">
                                    <a class="custom-button active" href="{{ __(@$banner->data_values->left_button_link) }}">{{ __(@$banner->data_values->left_button) }}</a>
                                    <a class="custom-button" href="{{ __(@$banner->data_values->right_button_link) }}">{{ __(@$banner->data_values->right_button) }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="banner-prev"><i class="fas fa-angle-left"></i></div>
    <div class="banner-next"><i class="fas fa-angle-right"></i></div>
</section>
