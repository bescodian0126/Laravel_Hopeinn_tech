@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">{{ __($pageTitle) }}</h3>
        </div>
        @if(!count($plans))
            <div class = "row">
                <h4>No Plan Launched</h4>
            </div>
        @else
        <div class="row">
            @foreach ($plans as $data)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card custom--card dashboard-plan-card">
                        <div class="card-body">
                            <div class="pricing-table mb-4 text-center">
                                <h4 class="package-name mb-2 text-center">
                                    <strong>{{ __($data->name) }}</strong>
                                </h4>
                                <span class="price text--dark fw-bold d-block">
                                    {{ $general->cur_sym }}{{ showAmount($data->price) }}
                                </span>
                                <hr>
                                <ul class="package-features-list mt-3">
                                    <li>
                                        <i class="fas fa-check bg--success"></i>
                                        <span>@lang('System Fee'): {{ showAmount($data->fee) }}%</span>
                                        <span class="icon" data-bs-toggle="modal" data-bs-target="#bvInfoModal">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check bg--success"></i>
                                        <span>@lang('Distribution'):
                                            {{ $general->cur_sym }}{{ showAmount($data->distribution) }}</span>
                                        <span class="icon" data-bs-toggle="modal" data-bs-target="#refComInfoModal">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                    <li>
                                        <i
                                            class="fas @if (showAmount($data->size) != 0) fa-check bg--success @else fa-times bg--danger @endif"></i>
                                        <span>@lang('Plan Size'):
                                            <!-- {{ $general->cur_sym }} -->
                                            3 * {{ intval(showAmount($data->size)) }}</span>
                                        <span class="icon" data-bs-toggle="modal" data-bs-target="#treeComInfoModal">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            @if ($data->active)
                                <button class="btn btn--success w-100 disabled mt-2" type="button">
                                    @lang('Current Running')
                                </button>
                            @elseif ($data->refActive)
                                <button class="btn btn-warning subscribeBtn w-100 mt-2" data-amount="{{ getAmount($data->price) }}" data-id="{{ $data->id }}" data-size="{{$data->size}}" data-ref="{{$user->ref_user}}" data-position="{{$user->position}}"  type="button">
                                    @lang('Invited')
                                </button>
                            @else
                                <button class="btn btn--base subscribeBtn w-100 mt-2" data-amount="{{ getAmount($data->price) }}" data-id="{{ $data->id }}" data-size="{{$data->size}}" data-ref="rootuser" data-position="{{$user->position}}"  type="button">
                                    @lang('Purchase')
                                </button>
                            @endif
                        </div>

                    </div><!-- card end -->
                </div>
            @endforeach
        </div>
        @if ($plans->hasPages())
            {{ paginateLinks($plans) }}
        @endif
        @endif
    </div>
    @include($activeTemplate . 'partials.plan_modals')
@endsection
