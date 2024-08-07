@php
    $works = getContent('how_it_works.element');
    $workCaption = getContent('how_it_works.content', true);
@endphp
<section class="feature-section padding-top padding-bottom">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$workCaption->data_values->heading) }}</h2>
            <p>{{ __(@$workCaption->data_values->sub_heading) }}</p>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($works as $k => $data)
                <div class="col-xl-4 col-md-6 col-sm-10">
                    <div class="feature-item">
                        <div class="feature-header">
                            <div class="icon">
                                <?php echo @$data->data_values->icon; ?>
                            </div>
                            <h6 class="title">{{ __(@$data->data_values->title) }}</h6>
                        </div>
                        <div class="feature-body">
                            <p>{{ __(@$data->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>