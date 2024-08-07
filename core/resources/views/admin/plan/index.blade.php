@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Size')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('System Fee')</th>
                                    <th>@lang('Distribution')</th>
                                    <th>@lang('Reenter Fee')</th>
                                    <th>@lang('Advance Fee')</th>
                                    <th>@lang('Status')</th>
                                    <th style = "text-align : center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $key => $plan)
                                    <tr>
                                        <td>{{ __($plan->name) }}</td>
                                        <td>3 * {{ __($plan->size) }}</td>
                                        <td>{{ showAmount($plan->price) }} {{ __($general->cur_text) }}</td>
                                        <!-- <td>{{ $plan->bv }}</td> -->
                                        <td> {{ showAmount($plan->fee) }} %</td>

                                        <td>
                                            {{ showAmount($plan->distribution) }} {{ __($general->cur_text) }}
                                        </td>
                                        <td>
                                            {{ $plan->reenter_fee }}
                                        </td>
                                        <td>
                                            {{ $plan->advance_fee }}
                                        </td>
                                        <td>
                                            @php echo $plan->statusBadge @endphp
                                        </td>

                                        <td style = "text-align : center">
                                            <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $plan->id }}" data-name="{{ $plan->name }}" data-bv="{{ $plan->bv }}" data-price="{{ getAmount($plan->price) }}" data-reenter_fee="{{ getAmount($plan->reenter_fee) }}" data-advance_fee="{{ getAmount($plan->advance_fee) }}" data-fee="{{ getAmount($plan->fee) }}" data-size="{{$plan->size}}" data-distribution="{{ getAmount($plan->distribution) }}" data-original-title="@lang('Edit')" type="button">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>

                                            @if ($plan->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this plan?')" data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this plan?')" data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                    <i class="la la-eye-slash"></i> @lang('Disable')
                                                </button>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($plans) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--    edit modal --}}
    <div class="modal fade" id="edit-plan" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit plan')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.plan.save') }}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control name" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Price') </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                    <input class="form-control price price_ctrl_edit" name="price" type="number" step="any" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Plan Size</label>
                            <select name="size" class="form-control size_ctrl_edit" required>
                                <option value="3">@lang('3 * 3')</option>
                                <option value="4">@lang('3 * 4')</option>
                                <option value="5">@lang('3 * 5')</option>
                                <option value="6">@lang('3 * 6')</option>
                            </select>
                        </div>
                        <div class = "form-group">
                            <label>System fee (%)</label>
                            <input class = "form-control fee fee_ctrl_edit" name = "fee" type = "number" />
                        </div>
                        <!-- <div class="form-group">
                            <label> @lang('Business Volume (BV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('When someone buys this plan, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control" name="bv" type="number" type="number" required>
                        </div> -->
                        <div class="form-group">
                            <label> @lang('Distribution')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('If a user who subscribed to this plan, refers someone and if the referred user buys a plan, then he will get this amount.')"></i>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control distribution distribution_ctrl_edit" name="distribution" type="number" step="any" required>
                            </div>
                        </div>
                        <div class = "form-group">
                            <label>ReEnter fee (%)</label>
                            <input class = "form-control reenter_fee" name = "reenter_fee" type = "number" />
                        </div>
                        <div class = "form-group">
                            <label>Advance fee (%)</label>
                            <input class = "form-control advance_fee" name = "advance_fee" type = "number" />
                        </div>
                        <!-- <div class="form-group">
                            <label> @lang('Tree Commission')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('When someone buys this plan, all of his ancestors will get this amount.')"></i>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control" name="tree_com" type="number" step="any" required>
                            </div>
                        </div> -->

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Add New Plan -->

    <div class="modal fade" id="add-plan" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New plan')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.plan.save') }}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Price') </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                    <input class="form-control price_ctrl" name="price" type="number" step="any" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Plan Size</label>
                            <select name="size" class="form-control size_ctrl" required>
                                <option value="3">@lang('3 * 3')</option>
                                <option value="4">@lang('3 * 4')</option>
                                <option value="5">@lang('3 * 5')</option>
                                <option value="6">@lang('3 * 6')</option>
                            </select>
                        </div>
                        <div class = "form-group">
                            <label>System fee (%)</label>
                            <input class = "form-control fee_ctrl" name = "fee" type = "number" placeholder = "10%" value="10"/>
                        </div>
                        <!-- <div class="form-group">
                            <label> @lang('Business Volume (BV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('When someone buys this plan, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control" name="bv" type="number" type="number" required>
                        </div> -->
                        <div class="form-group">
                            <label> @lang('Distribution')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('If a user who subscribed to this plan, refers someone and if the referred user buys a plan, then he will get this amount.')"></i>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control distribution_ctrl" name="distribution" type="number" step="any" required>
                            </div>
                        </div>                       
                        <div class = "form-group">
                            <label>ReEnter Fee (%)</label>
                            <input class = "form-control" name = "reenter_fee" type = "number" step="any"/>
                        </div>
                        <div class = "form-group">
                            <label>Advance Fee (%)</label>
                            <input class = "form-control" name = "advance_fee" type = "number" step="any"/>
                        </div>
                        <!-- <div class="form-group">
                            <label> @lang('Tree Commission')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('When someone buys this plan, all of his ancestors will get this amount.')"></i>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control" name="tree_com" type="number" step="any" required>
                            </div>
                        </div> -->

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary add-plan" type="button">
        <i class="la la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.edit').on('click', function() {
                var modal = $('#edit-plan');
                modal.find('.name').val($(this).data('name'));
                modal.find('.price').val($(this).data('price'));
                modal.find('.fee').val($(this).data('fee'));
                modal.find('.reenter_fee').val($(this).data('reenter_fee'));
                modal.find('.advance_fee').val($(this).data('advance_fee'));
                modal.find('.distribution').val($(this).data('distribution'));
                // modal.find('.tree_com').val($(this).data('tree_com'));
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.size_ctrl_edit').val($(this).data('size'));
                modal.modal('show');
            });

            $('.add-plan').on('click', function() {
                var modal = $('#add-plan');
                modal.modal('show');
            });

            function calculateDistribution() {
                // Get the values of price and fee
                var price = parseFloat($('.price_ctrl').val()) || 0;
                var fee = parseFloat($('.fee_ctrl').val()) || 0;
                var size =parseInt($('.size_ctrl').val()) || 3;

                // Calculate distribution using the given formula
                var distribution = (price - fee / 100 * price) / size;

                // Set the distribution value in the input field
                $('.distribution_ctrl').val(distribution.toFixed(2)); // toFixed(2) keeps two decimal places
            }

            // Attach event listeners to the price and fee input fields
            $('.price_ctrl, .fee_ctrl').on('input', calculateDistribution);
            $('.size_ctrl').on('change', calculateDistribution);

            // Initial calculation in case there are pre-filled values
            calculateDistribution();

            function calculateDistribution_edit() {
                // Get the values of price and fee
                var price = parseFloat($('.price_ctrl_edit').val()) || 0;
                var fee = parseFloat($('.fee_ctrl_edit').val()) || 0;
                var size =parseInt($('.size_ctrl_edit').val()) || 3;

                // Calculate distribution using the given formula
                var distribution = (price - fee / 100 * price) / size;

                // Set the distribution value in the input field
                $('.distribution_ctrl_edit').val(distribution.toFixed(2)); // toFixed(2) keeps two decimal places
            }

            // Attach event listeners to the price and fee input fields
            $('.price_ctrl_edit, .fee_ctrl_edit').on('input', calculateDistribution_edit);
            $('.size_ctrl_edit').on('change', calculateDistribution_edit);

            // Initial calculation in case there are pre-filled values
        })(jQuery);
    </script>
@endpush
