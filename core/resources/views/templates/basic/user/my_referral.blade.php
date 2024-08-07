@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">@lang('My Referrals')</h3>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom--card">
                    @if (!blank($logs))
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table--responsive--md table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Username')</th>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Email')</th>
                                            <th>@lang('Join Date')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $data)
                                            <tr>
                                                <td>{{ $data->username }}</td>
                                                <td>{{ $data->fullname }}</td>
                                                <td>{{ showEmailAddress($data->email) }}</td>
                                                <td>
                                                    @if ($data->created_at != '')
                                                        {{ showDateTime($data->created_at) }}
                                                    @else
                                                        @lang('Not Assign')
                                                    @endif
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
    </div>
@endsection
