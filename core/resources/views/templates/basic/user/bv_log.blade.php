@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">{{ __($pageTitle) }}</h3>
        </div>
        <div class="row">
            <div class="col-lg-12">

                @if (!blank($logs))
                    <div class="table-responsive">
                        <table class="table--responsive--md table">
                            <thead>
                                <tr>
                                    <th>@lang('BV')</th>
                                    <th>@lang('Position')</th>
                                    <th>@lang('Detail')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $data)
                                    <tr>
                                        <td class="budget">
                                            <strong
                                                @if ($data->trx_type == '+') class="text--success" @else class="text--danger" @endif>
                                                {{ $data->trx_type == '+' ? '+' : '-' }}
                                                {{ getAmount($data->amount) }}</strong>
                                        </td>

                                        <td>
                                            @if ($data->position == 1)
                                                <span class="badge badge--success">@lang('Left')</span>
                                            @else
                                                <span class="badge badge--primary">@lang('Right')</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->details }}</td>
                                        <td>
                                            {{ showDateTime($data->created_at, 'M d Y @g:i:a') }}
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
                @else
                    <div class="card-body text-center">
                        <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                    </div>
                @endif
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
