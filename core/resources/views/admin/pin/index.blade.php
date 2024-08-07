@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User|Admin')</th>
                                    @if (request()->routeIs('admin.pin.used'))
                                        <th>@lang('Username')</th>
                                    @endif
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Pin')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Creations Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pins as $pin)
                                    <tr>
                                        <td>
                                            @if ($pin->generate_user_id)
                                                {{ __($pin->details) }}
                                                <br>
                                                <span class="small text-center">
                                                    <a
                                                        href="{{ route('admin.users.detail', $pin->generate_user_id) }}"><span>@</span><span>{{ $pin->createUser->username }}</span></a>
                                                </span>
                                            @else
                                                {{ __($pin->details) }}
                                                <br>
                                                <span class="small">
                                                    <span>@lang('admin')</span>
                                                </span>
                                            @endif
                                        </td>
                                        @if (request()->routeIs('admin.pin.used'))
                                            <td>
                                                <span>{{ __($pin->user->fullname) }}</span>
                                                <br>
                                                <span class="small">
                                                    <a
                                                        href="{{ route('admin.users.detail', $pin->user_id) }}"><span>@</span>{{ __($pin->user->username) }}</a>
                                                </span>
                                            </td>
                                        @endif
                                        <td>
                                            <span>{{ getAmount($pin->amount) }}
                                                {{ __($general->cur_text) }}</span>
                                        </td>
                                        <td>
                                            {{ __($pin->pin) }}
                                        </td>
                                        <td>
                                            @if ($pin->status == 1)
                                                <span class="badge badge--success">@lang('Used')</span>
                                                <br>
                                                {{ diffForHumans($pin->updated_at) }}
                                            @elseif($pin->status == 0)
                                                <span class="badge badge--danger">@lang('Unused')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($pin->created_at) }} <br>
                                            {{ diffForHumans($pin->created_at) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($pins->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($pins) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="addModalPin" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Created Pin')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.pin.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">@lang('Amount')</label>
                            <div class="input-group mb-3">
                                <input type="number" id="amount" class="form-control" placeholder="@lang('Enter Amount')" name="amount" step="any" required="">
                                <div class="input-group-text">
                                    {{ __($general->cur_text) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="number">@lang('Total Number of Pin')</label>
                            <input type="number" class="form-control" name="number" placeholder="@lang('Enter Number')"
                                required="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Created')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search pin" />
    <button class="btn btn-outline--primary addPin"><i class="las la-paper-plane"></i>@lang('Created Pin')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addPin').on('click', function() {
                $('#addModalPin').modal('show');
            });
        })(jQuery);
    </script>
@endpush
