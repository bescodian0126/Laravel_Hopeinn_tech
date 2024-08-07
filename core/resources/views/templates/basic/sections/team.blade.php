@php
    $teamTitle = getContent('team.content', true);
    $teams = getContent('team.element');
@endphp
<section class="team-section padding-top padding-bottom section-bg">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$teamTitle->data_values->heading) }}</h2>
            <p>{{ __(@$teamTitle->data_values->sub_heading) }}</p>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($teams as $team)
                <div class="col-lg-3 col-md-6 col-sm-9">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ getImage('assets/images/frontend/team/' . @$team->data_values->image, '525x615') }}" alt="team">
                        </div>
                        <div class="team-content">
                            <h6 class="title">
                                {{ __(@$team->data_values->name) }}
                            </h6>
                            <span class="info">{{ __(@$team->data_values->designation) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
