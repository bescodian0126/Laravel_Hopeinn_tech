@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contact = getContent('contact_us.content', true);
    @endphp
    <div class="contact-info padding-top">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Email Address')</h6>
                                <ul>
                                    <li>
                                        <a href="Mailto:{{ @$contact->data_values->email_address }}">{{ @$contact->data_values->email_address }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <i class="fa fa-building"></i>
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Office Address')</h6>
                                <ul>
                                    <li>
                                        {{ __(@$contact->data_values->contact_details) }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <i class="fa fa-phone-square"></i>
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Phone Number')</h6>
                                <ul>
                                    <li>
                                        <a href="Tel:{{ @$contact->data_values->contact_number }}">{{ @$contact->data_values->contact_number }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="contact-section padding-top padding-bottom">
        <div class="container">
            <div class="row">

                <div class="col-lg-7">

                    <div class="contact-form-wrapper rounded bg-white shadow-sm">
                        <div class="section-header left-style mb-4">
                            <h2 class="title mb-4">{{ __(@$contact->data_values->heading) }}</h2>
                            <p>{{ __(@$contact->data_values->short_details) }}</p>
                        </div>
                        <form class="contact-form verify-gcaptcha" method="post" action="{{ route('contact') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Name')</label>
                                    <input id="name" name="name" type="text" value="{{ old('name',@$user->fullname) }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Subject')</label>
                                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Email')</label>
                                    <input id="email" name="email" type="email" value="{{ old('email',@$user->name) }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Message')</label>
                                    <textarea id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-lg-12">
                                    <x-captcha isCustom="true" />
                                </div>

                                <div class="col-lg-12 form-group">
                                    <input class="cmn-btn" type="submit" value="@lang('Submit Now')">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img class="wow slideInRight" src="{{ getImage('assets/images/frontend/contact_us/' . @$contact->data_values->background_image, '650x780') }}" alt="contact">
                </div>
            </div>
        </div>
    </section>

@endsection
