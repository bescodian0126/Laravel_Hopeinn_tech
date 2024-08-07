@php
    $counterCaption = getContent('counter.content', true);
    $counters = getContent('counter.element', false, null, true);
@endphp
<div class="counter-section padding-top padding-bottom bg-overlay bg_fixed bg_img" data-background="{{ getImage('assets/images/frontend/counter/' . @$counterCaption->data_values->background_image, '1920x400') }}">
    <div class="container">
        <div class="counter-wrapper">
            @foreach ($counters as $counter)
                <div class="counter-item">
                    <div class="counter-header">
                        <h2 class="title">{{ __(@$counter->data_values->counter_value) }}</h2>
                    </div>
                    <h6 class="subtitle">{{ @__($counter->data_values->title) }} </h6>
                </div>
            @endforeach
        </div>
    </div>
</div>
