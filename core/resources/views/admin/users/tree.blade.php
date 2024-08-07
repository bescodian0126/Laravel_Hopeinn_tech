@extends('admin.layouts.app')
@section('panel')
    
    <div class="card">
        <div class="card-body">
            @php
                $loopNumber = 1;
                $totalLoop = 0;
                $classloopNumber = 1;
            @endphp
            @for ($i = 1; $i <= 4; $i++)
                <div class="row justify-content-center llll text-center">
                    @for ($in = 1; $in <= $loopNumber; $in++)
                        <div class="w-{{ $classloopNumber }}">
                            @php 
                                echo $mlm->showSingleUserinTree($tree[$mlm->getHands()[$totalLoop]]); 
                            @endphp
                        </div>
                        @php
                            $totalLoop++;
                        @endphp
                    @endfor
                </div>
                @php
                    $loopNumber = $loopNumber * 3;
                    $classloopNumber = $classloopNumber * 2;
                @endphp
            @endfor
        </div>
    </div>

    <div class="modal fade user-details-modal-area" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-details-modal">
                        <div class="user-details-header ">
                            <div class="thumb"><img class="w-h-100-p tree_image" src="#" alt="*"></div>
                            <div class="content">
                                <a class="user-name tree_url tree_name" href=""></a>
                                <span class="user-status tree_status"></span>
                                <span class="user-status tree_plan"></span>
                            </div>
                        </div>
                        <div class="user-details-body text-center">

                            <h6 class="my-3">@lang('Referred By'): <span class="tree_ref"></span></h6>

                            <table class="table--responsive--md table">
                                <thead>
                                    <th>&nbsp;</th>
                                    <th>@lang('LEFT')</th>
                                    <th>@lang('CENTER')</th>
                                    <th>@lang('RIGHT')</th>
                                </thead>

                                <tr>
                                    <td>@lang('Free Member')</td>
                                    <td><span class="lfree"></span></td>
                                    <td><span class="cfree"></span></td>
                                    <td><span class="rfree"></span></td>
                                </tr>

                                <tr>
                                    <td>@lang('Paid Member')</td>
                                    <td><span class="lpaid"></span></td>
                                    <td><span class="cpaid"></span></td>
                                    <td><span class="rpaid"></span></td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <link href="{{asset('assets/admin/css/tree.css')}}" rel="stylesheet">
@endpush
@push('script')
    <script>
        'use strict';
        (function($) {
            $('.showDetails').on('click', function() {
                var modal = $('#exampleModalCenter');
                $('.tree_name').text($(this).data('name'));
                $('.tree_url').attr({
                    "href": $(this).data('treeurl')
                });
                $('.tree_status').text($(this).data('status'));
                $('.tree_plan').text($(this).data('plan'));
                $('.tree_image').attr({
                    "src": $(this).data('image')
                });
                $('.user-details-header').removeClass('Paid');
                $('.user-details-header').removeClass('Free');
                $('.user-details-header').addClass($(this).data('status'));
                $('.tree_ref').text($(this).data('refby'));
                $('.lbv').text($(this).data('lbv'));
                $('.rbv').text($(this).data('rbv'));
                $('.lpaid').text($(this).data('lpaid'));
                $('.rpaid').text($(this).data('rpaid'));
                $('.lfree').text($(this).data('lfree'));
                $('.rfree').text($(this).data('rfree'));
                $('#exampleModalCenter').modal('show');
            });
        })(jQuery)
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by username" />
@endpush
