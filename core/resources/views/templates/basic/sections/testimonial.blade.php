@php
    $testimonialCaption = getContent('testimonial.content', true);
    $testimonials = getContent('testimonial.element');
@endphp
<section class="client-section padding-top padding-bottom">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$testimonialCaption->data_values->heading) }}</h2>
            <p>{{ __(@$testimonialCaption->data_values->sub_heading) }}</p>
        </div>
        <div class="client-slider">
            <div class="swiper-wrapper">
                @foreach ($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="client-item">
                            <blockquote>
                                {{ __(@$testimonial->data_values->quote) }}
                            </blockquote>
                            <div class="author">
                                <div class="author-thumb">
                                    <img src="{{ getImage('assets/images/frontend/testimonial/' . @$testimonial->data_values->image, '150x150') }}" alt="client">
                                </div>
                                <div class="author-content">
                                    <h6 class="title">{{ __(@$testimonial->data_values->author) }}</h6>
                                    <span>{{ __(@$testimonial->data_values->designation) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
