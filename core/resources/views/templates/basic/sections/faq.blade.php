@php
    $faqTitle = getContent('faq.content', true);
    $faqs = getContent('faq.element');
@endphp
<section class="faq-section padding-top padding-bottom section-bg">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$faqTitle->data_values->heading) }}</h2>
            <p>{{ __(@$faqTitle->data_values->sub_heading) }}</p>
        </div>
        <div class="row mb--20">
            <div class="col-lg-12">
                <div class="faq-wrapper style-two">
                    @foreach ($faqs as $key => $faq)
                        <div class="faq-item">
                            <div class="faq-title">
                                <h6 class="title">{{ __(@$faq->data_values->question) }}</h6>
                                <div class="right-icon"></div>
                            </div>
                            <div class="faq-content">
                                <p>{{ __(@$faq->data_values->answer) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
