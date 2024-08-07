@php
    $footer = getContent('footer.content', true);
    $socials = getContent('social_icon.element', false, false, true);
@endphp
<footer>
    <div class="footer-top">
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo"></a>
            </div>
            <p>{{ __(@$footer->data_values->short_details) }}</p>
            <ul class="social-icons">
                @foreach ($socials as $social)
                    <li>
                        <a href="{{ @$social->data_values->url }}" title="{{ @$social->data_values->title }}" target="_blank">
                            @php echo @$social->data_values->icon; @endphp
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>@lang('Copyright') {{ date('Y') }} &copy; @lang('All Rights Reserved by') <a href="{{ route('home') }}">{{ __($general->site_name) }}</a></p>
    </div>
</footer>
