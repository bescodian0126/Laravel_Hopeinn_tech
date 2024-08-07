<div class="dashboard-sidebar" id="dashboard-sidebar">
    <button class="btn-close dash-sidebar-close d-xl-none"></button>
    <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_dark.png')) }}" alt="images">
    </a>
    <div class="bg--lights">
        <div class="profile-info">
            <p class="fs--13px mb-3 fw-bold">@lang('ACCOUNT BALANCE')</p>
            <h4 class="usd-balance text--base mb-2 fs--30">
                {{ showAmount(auth()->user()->balance) }} 
                <sub class="top-0 fs--13px">{{ __($general->cur_text) }}</sub>
            </h4>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('user.deposit.index') }}" class="btn btn--base btn--smd">@lang('Deposit')</a>
                <a href="{{ route('user.withdraw') }}" class="btn btn--secondary btn--smd">@lang('Withdraw')</a>
            </div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/dashboard.png') }}" alt="icon"> @lang('Dashboard')
            </a>
        </li>
        <li>
            <a href="{{ route('user.plan.index') }}" class="{{ menuActive('user.plan.index') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Plan')
            </a>
        </li>
        <li>
            <a href="{{ route('user.bv.log') }}" class="{{ menuActive('user.bv.log') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/bv_log.png') }}" alt="icon"> @lang('Bv Log')
            </a>
        </li>
        <li>
            <a href="{{ route('user.my.referral') }}" class="{{ menuActive('user.my.referral') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/referral.png') }}" alt="icon"> @lang('My Referrals')
            </a>
        </li>
        <li>
            <a href="{{ route('user.binary.tree') }}" class="{{ menuActive('user.binary.tree') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/tree.png') }}" alt="icon"> @lang('My Tree')
            </a>
        </li>
        <li>
            <a href="{{ route('user.deposit.index') }}" class="{{ menuActive('user.deposit*') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"> @lang('Deposit')
            </a>
        </li>
        <li>
            <a href="{{ route('user.withdraw') }}" class="{{ menuActive('user.withdraw*') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/withdraw.png') }}" alt="icon"> @lang('Withdraw')
            </a>
        </li>
        @if ($general->balance_transfer == 1)
            <li>
                <a href="{{ route('user.balance.transfer') }}" class="{{ menuActive('user.balance.transfer') }}">
                    <img src="{{ asset($activeTemplateTrue.'users/images/icon/transfer.png') }}" alt="icon"> @lang('Balance Transfer')
                </a>
            </li>
        @endif
        @if ($general->epin == 1)
            <li>
                <a href="{{ route('user.epin.recharge') }}" class="{{ menuActive('user.epin.recharge') }}">
                    <img src="{{ asset($activeTemplateTrue.'users/images/icon/epin.png') }}" alt="icon"> @lang('E-pin Recharge')
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/transactions.png') }}" alt="icon"> @lang('Transactions')
            </a>
        </li>
        @if ($general->user_ranking)
            <li>
                <a href="{{ route('user.ranking') }}" class="{{ menuActive('user.ranking') }}">
                    <img src="{{ asset($activeTemplateTrue . 'users/images/icon/ranking.png') }}" alt="icon"> @lang('Ranking')
                </a>
            </li>
        @endif
        
        <li>
        <a href="{{ route('user.task.index') }}" class="{{ menuActive(['user.task.index', 'user.task.view', 'user.task.open']) }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/tasks.png') }}" alt="icon"> @lang('Task')
            </a>
        </li>

        <li>
        <li>
            <a href="{{ route('ticket.index') }}" class="{{ menuActive(['ticket.index', 'ticket.view', 'ticket.open']) }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/ticket.png') }}" alt="icon"> @lang('Support Ticket')
            </a>
        </li>
        <li>
            <a href="{{ route('user.twofactor') }}" class="{{ menuActive('user.twofactor') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/2fa.png') }}" alt="icon"> @lang('2FA')
            </a>
        </li>
        <li>
            <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/profile.png') }}" alt="icon"> @lang('Profile')
            </a>
        </li>
        <li>
            <a href="{{ route('user.change.password') }}" class="{{ menuActive('user.change.password') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/password.png') }}" alt="icon"> @lang('Change Password')
            </a>
        </li>
        <li>
            <a href="{{ route('user.logout') }}" class="{{ menuActive('user.logout') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/logout.png') }}" alt="icon"> @lang('Logout')
            </a>
        </li>
    </ul>
</div>
