@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="dashboard-title">@lang('KYC Submission') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('The system requires you to submit KYC (know your client) information. Your submitted data will be verified by the system\s admin. If all of your information is correct, the admin will approve the KYC data and you\'ll be able to make withdrawal requests')"></i></h3>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="{{route('user.kyc.submit')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <x-viser-form identifier="act" identifierValue="kyc" />

                            <div class="form-group">
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .form-group{
            margin-bottom: 12px;
        }
    </style>
@endpush