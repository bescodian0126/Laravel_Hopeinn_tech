@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12 mb-30">
            <div class="card bl--5-primary">
                <div class="card-body">
                    <p class="text--primary">@lang('If the logo and favicon are not changed after you update from this page, please') <span class="text--danger">@lang('clear the cache')</span> @lang('from your browser. As we keep the filename the same after the update, it may show the old image for the cache. usually, it works after clear the cache but if you still see the old logo or favicon, it may be caused by server level or network level caching. Please clear them too.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-6 col-xl-3">
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="">
                                            <div class="row">
                                                <div class="avatar-preview">
                                                    <div class="profilePicPreview logoPicPrev" style="background-image: url({{ getImage(getFilePath('logoIcon') . '/logo_dark.png', '?' . time()) }})">
                                                        <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input class="profilePicUpload" id="profilePicUpload1" name="logo_dark" type="file" accept=".png, .jpg, .jpeg">
                                                        <label class="bg--primary" for="profilePicUpload1">@lang('Select Dark Logo')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-xl-3">
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="">
                                            <div class="row">
                                                <div class="avatar-preview">
                                                    <div class="profilePicPreview logoPicPrev bg--dark" style="background-image: url({{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }})">
                                                        <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input class="profilePicUpload" id="profilePicUploadDark" name="logo" type="file" accept=".png, .jpg, .jpeg">
                                                        <label class="bg--primary" for="profilePicUploadDark">@lang('Select Logo')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-xl-6">
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="profilePicPreview logoPicPrev" style="background-image: url({{ getImage(getFilePath('logoIcon') . '/favicon.png', '?' . time()) }})">
                                                        <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mt-sm-0 mt-4">
                                                    <div class="profilePicPreview logoPicPrev bg--dark" style="background-image: url({{ getImage(getFilePath('logoIcon') . '/favicon.png', '?' . time()) }})">
                                                        <button class="remove-image" type="button"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input class="profilePicUpload" id="profilePicUpload2" name="favicon" type="file" accept=".png">
                                            <label class="bg--primary" for="profilePicUpload2">@lang('Select Favicon')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
