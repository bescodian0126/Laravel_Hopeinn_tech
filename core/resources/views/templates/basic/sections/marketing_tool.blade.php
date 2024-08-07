@php
    $marketingTools = getContent('marketing_tool.element');
@endphp
<section class="feature-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            @foreach ($marketingTools as $marketingTool)
                <div class="col-xl-12">
                    <div class="feature-item">
                        <div class="feature-header">
                            <div class="icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h6 class="title">{{ __(@$marketingTool->data_values->title) }}</h6>
                        </div>
                        <div class="feature-body">
                            <p>@php echo @$marketingTool->data_values->description; @endphp</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
