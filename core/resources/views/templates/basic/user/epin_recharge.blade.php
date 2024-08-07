@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <div class="d-flex justify-content-between">
                <h3 class="mb-2">@lang('E-pin Recharge')</h3>
                <span>
                    <a href="{{ route('user.recharge.log') }}" class="btn btn--base btn--smd">
                        @lang('Recharge Logs')
                    </a>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <form action="{{ route('user.erecharge') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="@lang('Enter Pin')"
                                        name="pin" required>
                                    <button type="submit" class="btn btn--base">@lang('Recharge Now')</button>
                                </div>
                            </form>
                            <button data-bs-toggle="modal" data-bs-target="#generatePin" class="btn btn--base"><i
                                    class="fa fa-fw fa-paper-plane"></i> @lang('Create Pin')</button>
                        </div>
                    </div>
                </div>

                <div class="accordion table--acordion mt-4" id="transactionAccordion">
                    @forelse($pins as $pin)
                        <div class="accordion-item transaction-item">
                            <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#c-{{ $loop->iteration }}">
                                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                        <div class="left">
                                            <div
                                                class="icon tr-icon @if ($pin->status == 1) icon-success @else icon-danger @endif">
                                                <i class="las la-long-arrow-alt-right"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="trans-title">
                                                    @if ($pin->user_id)
                                                        {{ __($pin->user->username) }}
                                                    @else
                                                        @lang('N/A')
                                                    @endif
                                                </h6>
                                                <span
                                                    class="text-muted font-size--14px mt-2">{{ showDateTime($pin->created_at, 'M d Y @g:i:a') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                        <p class="text-muted font-size--14px"><b>{{ $pin->pin }}</b></p>
                                    </div>
                                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                        <p>
                                            <b>{{ showAmount($pin->amount) }} {{ __($general->cur_text) }}</b><br>

                                        </p>

                                    </div>
                                </button>
                            </h2>
                            <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                                data-bs-parent="#transactionAccordion">
                                <div class="accordion-body">
                                    <ul class="caption-list">
                                        <li>
                                            <span class="caption">@lang('Pin')</span>
                                            <span class="value">{{ $pin->pin }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Amount')</span>
                                            <span class="value">{{ showAmount($pin->amount) }}
                                                {{ __($general->cur_text) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Details')</span>
                                            <span class="value">{{ __($pin->details) }}</span>
                                        </li>
                                        <li>
                                            <span class="caption">@lang('Status')</span>
                                            <span class="value">
                                                @if ($pin->status == 1)
                                                    <span class="badge badge--success">@lang('Used')</span>
                                                    <br>
                                                    {{ diffforhumans($pin->updated_at) }}
                                                @elseif($pin->status == 0)
                                                    <span class="badge badge--danger">@lang('Unused')</span>
                                                @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- transaction-item end -->
                    @empty
                        <div class="accordion-body text-center">
                            <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                        </div>
                    @endforelse
                </div>

                @if ($pins->hasPages())
                    <div class="mt-4">
                        {{ paginateLinks($pins) }}
                    </div>
                @endif

            </div>
        </div>

    </div>

    <div class="modal fade custom--modal" id="generatePin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Created Pin')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.pin.generate') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="contact-form-group">
                            <div class="input-group mb-3">
                                <input type="number" class="form-control form--control" name="amount"
                                    placeholder="@lang('Enter Amount')" value="{{ old('amount') }}" step="any"
                                    required="">
                                <div class="input-group-text">
                                    {{ __($general->cur_text) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--base">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
