@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3>@lang('Rankings')</h3>
        </div>
        <div class="row justify-content-center">
            @php $nextRanking = $userRankings->where('id', '>', $user->user_ranking_id)->first(); @endphp
            @if ($nextRanking)
                <div class="col-md-12 mb-4">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="row gy-4 align-items-center">
                                <div class="col-lg-4 col-md-6">
                                    <div class="d-flex align-items-center raking-invest">
                                        <img class="me-3" src="{{ getImage(getFilePath('userRanking') . '/' . $nextRanking->icon, getFileSize('userRanking')) }}" alt="image">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="raking-common text-center">
                                        <span>@lang('BV Left')</span>
                                        <h5>{{ $user->userExtra->total_bv_left }} / {{ $nextRanking->bv_left }}</h5>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="raking-common text-center">
                                        <span>@lang('BV Right')</span>
                                        <h5>{{ $user->userExtra->total_bv_right }} / {{ $nextRanking->bv_right }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-12">
                <div class="row gy-4">
                    @foreach ($userRankings as $userRanking)
                        @if ($user->user_ranking_id >= $userRanking->id)
                            @php $progressPercent = 100; @endphp
                        @else
                            @php
                                $myBvLeftPercent = ($user->userExtra->total_bv_left / $userRanking->bv_left) * 100;
                                $myBvRightPercent = ($user->userExtra->total_bv_right / $userRanking->bv_right) * 100;
                                
                                $myBvLeftPercent = $myBvLeftPercent < 100 ? $myBvLeftPercent : 100;
                                $myBvRightPercent = $myBvRightPercent < 100 ? $myBvRightPercent : 100;
                                $progressPercent = ($myBvLeftPercent + $myBvRightPercent) / 2;
                            @endphp
                        @endif
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <div class="invest-badge text-center">
                                <div class="invest-badge__thumb">
                                    <div class="invest-badge__thumb__mask {{ @$nextRanking->id < $userRanking->id ? 'badge-lock' : '' }}" data-progress="{{ @$nextRanking->id < $userRanking->id ? 0 : $progressPercent }}">
                                        <img src="{{ getImage(getFilePath('userRanking') . '/' . $userRanking->icon, getFileSize('userRanking')) }}" alt="image">
                                    </div>
                                </div>
                                <h4 class="invest-badge__title">
                                    {{ __($userRanking->name) }}
                                </h4>
                                <p class=invest-badge__subtitle>@lang('Bonus') - {{ $general->cur_sym }}{{ showAmount($userRanking->bonus) }}</p>
                                <ul class="invest-badge__list invest-badge__details  invest-badge__details-{{ $loop->iteration % 4 == 0 ? 4 : $loop->iteration % 4 }} {{ $loop->iteration % 3 == 0 ? 'invest-badge__detail_one' : 'invest-badge__detail_two' }}">
                                    <li class="d-flex "><span>@lang('Level') </span>
                                        <span>: {{ $userRanking->level }}</span>
                                    </li>
                                    <li class="d-flex "><span>@lang('BV Left') </span>
                                        <span>: {{ showAmount($userRanking->bv_left) }}</span>
                                    </li>
                                    <li class="d-flex "><span>@lang('BV Left') </span>
                                        <span>: {{ showAmount($userRanking->bv_right) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            var elements = $('.invest-badge__thumb__mask');
            elements.each(function(index, element) {
                let progress = $(element).data('progress');
                element.style.setProperty('--before-height', `${progress}%`);
            });

        })(jQuery);
    </script>
@endpush
