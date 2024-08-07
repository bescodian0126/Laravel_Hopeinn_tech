@php
    $latestTrx = getContent('transactions.content', true);
    $deposits = App\Models\Deposit::orderby('id', 'desc')
        ->where('status', Status::PAYMENT_SUCCESS)
        ->take(10)
        ->with('user', 'user.plan')
        ->get();
    $withdraws = App\Models\Withdrawal::orderby('id', 'desc')
        ->where('status', Status::PAYMENT_SUCCESS)
        ->take(10)
        ->with('user', 'user.plan')
        ->get();
@endphp

<section class="transaction-section padding-top section-bg padding-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header">
                    <h2 class="title">{{ __(@$latestTrx->data_values->heading) }}</h2>
                    <p>{{ __(@$latestTrx->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div>
        <div class="tab deposit-tab">
            <ul class="tab-menu text-center">
                <li class="active custom-button">@lang('Latest Deposits')</li>
                <li class="custom-button">@lang('Latest Withdraws')</li>
            </ul>
            <div class="tab-area">
                <div class="tab-item active">
                    <div class="deposite-table">
                        <table>
                            <thead>
                                <tr class="bg-2">
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <div class="author">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$deposit->user->image, false, true) }}" alt="jpg">
                                                </div>
                                                <div class="content">{{ @$deposit->user->fullName }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ @$deposit->user->plan->name ?? 'No plan' }}</td>
                                        <td>
                                            {{ showDateTime($deposit->created_at, 'd F, Y') }}</td>
                                        <td>{{ showAmount($deposit->amount) }}
                                            {{ __($general->cur_text) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colSpan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-item">
                    <div class="deposite-table">
                        <table>
                            <thead>
                                <tr class="bg-2">
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdraws as $withdraw)
                                    <tr>
                                        <td>
                                            <div class="author">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$withdraw->user->image, false, true) }}" alt="jpg">
                                                </div>
                                                <div class="content">
                                                    {{ @$withdraw->user->fullName }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ @$deposit->user->plan->name ?? 'No plan' }}</td>
                                        <td>
                                            {{ showDateTime($withdraw->created_at, 'd F, Y') }}</td>
                                        <td>{{ showAmount($withdraw->amount) }}
                                            {{ __($general->cur_text) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colSpan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
