@php
    $serviceContent = getContent('service.content', true);
    $services = getContent('service.element');
@endphp
<section class="how-to-section padding-bottom padding-top section-bg">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$serviceContent->data_values->heading) }}</h2>
            <p>{{ __(@$serviceContent->data_values->sub_heading) }}</p>
        </div>
        <div class="row justify-content-center mb-30-none how-wrapper">
            @foreach ($services as $data)
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="how-item">
                        <div class="how-thumb">
                            <?php echo @$data->data_values->icon; ?>
                        </div>
                        <div class="how-content">
                            <h5 class="title">{{ __(@$data->data_values->title) }}</h5>
                            <p>{{ __(@$data->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>