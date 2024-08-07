@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="buy_sell_panel">
        <a class="panel" href = "{{route('user.energy_shop.buy')}}">
            <h2>Buy</h2>
            <img src="{{ asset($activeTemplateTrue . 'users/images/icon/buy_icon.png') }}" alt="Buy Icon">
        </a>
        <div class="middle-panel">
            <div class="electricity">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/electricity.png') }}" alt="Electricity Icon">
            </div>
            <div class="lightning">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/energy_icon.png') }}" alt="energy_icon Icon">
                <span>{{$user->energy}}</span>
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/energy_icon.png') }}" alt="energy_icon Icon">
            </div>
        </div>
        <a class="panel" href = "{{route('user.energy_shop.sell')}}">
            <h2>Sell</h2>
            <img src="{{ asset($activeTemplateTrue . 'users/images/icon/sell_icon.png') }}" alt="Sell Icon">
        </a>
    </div>

</div>

@endsection

@push('style')
<style>
    .buy_sell_panel {
        display: flex;
        justify-content: space-between;

        align-items: center;
        height: 60%;
        text-align: center;
    }

    .panel {
        width: 30%;
        border-radius: 20px;
        background-color: white;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        border: 1px dotted blue;
        cursor: pointer;
        transition: width 0.1s ease, background-color 0.1s ease;
    }

    .panel:hover {
        /* background-color: #31A004; */
        width: 32%;
        height: auto;
    }

    .panel h2 {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 50px;
        color: black;
        margin-bottom: 10px;
    }

    .panel img {
        width: 80%;
        height: auto;
    }

    .middle-panel {
        width: 30%;
        height: calc(40% * 1.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-left: 5%;
        margin-right: 5%;
    }

    .electricity {
        margin-bottom: 20px;
        width: 100%;
    }

    .electricity img {
        width: 50%;
        height: auto;
    }

    .lightning {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .lightning img {
        margin: 0;
        width: 30%;
        height: auto;
    }

    .lightning span {
        font-size: 1.5rem;
        margin: 0 0.1%;
        font-family: Arial, Helvetica, sans-serif;
    }

    .card img {
        width: 50px;
        height: 50px;

    }
</style>