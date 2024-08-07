@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">

                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Initiated')</th>
                                <th>@lang('Username')</th>
                                <th>@lang('Task Name')</th>
                                <th>@lang('Task Cost')</th>
                                <th>@lang('Reward')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>
                                        {{showDateTime($task->created_at)}} <br>  {{ diffForHumans($task->created_at) }}
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $task->user->fullname }}</span>
                                        <br>
                                        <span class="small"> <a href="{{ appendQuery('search',@$task->user->username) }}"><span>@</span>{{ $task->user->username }}</a> </span>
                                    </td>
                                    <td>
                                        {{$task->task->task_name}}
                                    </td>
                                    <td>
                                        {{$task->task->energy_cost}}
                                    </td>
                                    <td>
                                        {{$task->task->reward}}
                                    </td>
                                    <td>
                                        @php echo $task->statusBadge @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.task.details', $task->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                            <i class="la la-desktop"></i> @lang('Details')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                
                            </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
                @if ($tasks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($tasks) }}
                    </div>
                @endif
            </div>
            
        </div><!-- card end -->
    </div>
</div>

@endsection




@push('breadcrumb-plugins')
<x-search-form dateSearch='yes' />
@endpush

@push('script')
    <script>
            'use strict'
            var tasks = @json($tasks);
            console.log(tasks);
    </script>
@endpush
