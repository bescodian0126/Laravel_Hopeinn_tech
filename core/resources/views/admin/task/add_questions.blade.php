@extends('admin.layouts.app')

@section('panel')
<div class="col-lg-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table--light style--two table">
                    <thead>
                        <tr>
                            <th colspan="5">@lang('Question')</th>
                            <th>@lang('Answers')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($task->taskQuizzes as $quiz)
                            <tr>
                                <td colspan="5">{{ __($quiz->question) }}</td>

                                <td>{{!empty($quiz->answers) ? count($quiz->answers) : 0}}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline--success edit_question" data-toggle="tooltip"
                                        data-question="{{$quiz->question}}" data-question_id="{{$quiz->id}}">
                                        <i class="la la-pencil"></i>@lang('Edit Question')
                                    </button>
                                    <button class="btn btn-sm btn-outline--primary manage_answers" data-toggle="tooltip"
                                        data-question_id="{{$quiz->id}}" data-question="{{$quiz->question}}"
                                        data-answers="{{$quiz->answers}}">
                                        <i class="la la-pencil"></i>@lang('Manage Answers')
                                    </button>

                                    <button class="btn btn-sm btn-outline--danger delete_question" data-toggle="tooltip"
                                        data-question="{{$quiz->question}}" data-question_id="{{$quiz->id}}">
                                        <i class="la la-pencil"></i>@lang('Delete')
                                    </button>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                    </tbody>
                </table><!-- table end -->
            </div>
        </div>
    </div>
</div>

<!-- Add New Question Modal -->
<div class="modal fade" id="add_question" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New task')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.task.create_question') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control task_id" name="id" type="hidden">

                    <label>@lang('Add New Question')</label>
                    <textarea class="form-control" rows="3" name="new_question_area" id="new_question_area"></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Existing Question Modal -->
<div class="modal fade" id="edit_question" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New task')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.task.edit_question') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control task_id" name="id" type="hidden">

                    <label>@lang('Add New Question')</label>
                    <textarea class="form-control" rows="3" name="edit_question_area"
                        id="edit_question_area"></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn--success w-100 h-45" type="submit">@lang('Change')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Existing Question Modal -->
<div class="modal fade" id="delete_question" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Are you sure to delete this question?')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.task.delete_question') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control question_id" name="id" type="hidden">

                    <label>@lang('Add New Question')</label>
                    <textarea class="form-control" rows="3" name="delete_question_area" id="delete_question_area"
                        disabled></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn--danger w-100 h-45" type="submit">@lang('Delete')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Answers Modal -->
<div class="modal fade" id="manage_answers" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Manage answers')</h5>

                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form>
                @csrf
                <div class="modal-body">
                    <input class="form-control question_id" name="id" type="hidden">

                    <textarea class="form-control mb-4" rows="3" name="question_answers_area" id="question_answers_area"
                        disabled></textarea>
                    <label>@lang('Answers')</label>
                    <div class="table-responsive--md table-responsive mb-2">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th colspan="5">@lang('Answer')</th>
                                    <th>@lang('True or False')</th>
                                    <th style="">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="answers_area">

                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-sm btn-outline--primary add_answer mb-4" type="button" id="add_answer_btn">
                        <i class="la la-plus"></i>@lang('Add New Answer')
                    </button>
                </div>

                <div class="modal-footer">
                    <button class="btn btn--success w-100 h-45" type="submit"
                        data-bs-dismiss="modal" id="manage_answers_btn">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add New Answer modal-->
<div class="modal fade" id="add_new_answer_modal" role="dialog" tabindex="1">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Answer')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <input class="form-control question_id" name="id" id="new_answer_modal_question_id" type="hidden">
                    <label for="new_answer_area">Answer</label>
                    <textarea class="form-control" rows="2" name="new_answer_area" id="new_answer_area"></textarea>
                    <label for="new_select_true_false">Select if the answer is true or false</label>
                    <select class="form-select" name="new_select_true_false" id="new_select_true_false">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit"
                        id="add_new_answer_btn">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Answer Modal -->
<div class="modal fade" id="edit_answer_modal" role="dialog" tabindex="1">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Answer')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <input class="form-control question_id" name="id" id="edit_answer_id" type="hidden">
                    <label for="edit_answer_area">Answer</label>
                    <textarea class="form-control" rows="2" name="edit_answer_area" id="edit_answer_area"></textarea>
                    <label for="edit_select_true_false">Select if the answer is true or false</label>
                    <select class="form-select" name="edit_select_true_false" id="edit_select_true_false">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit"
                        id="update_answer_btn">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Answer Modal -->
<div class="modal fade" id="delete_answer_modal" role="dialog" tabindex="1">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Are you sure to delete this answer?')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <input class="form-control question_id" name="id" id="delete_answer_id" type= "hidden">
                    <label for="delete_answer_area">Answer</label>
                    <textarea class="form-control" rows="2" name="delete_answer_area" id="delete_answer_area" disabled></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn--danger w-100 h-45" type="submit"
                        id="delete_answer_btn">@lang('Delete')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast for message -->
<div aria-live="polite" aria-atomic="true" class="position-relative" style="min-height: 200px;">
    <div class="toast custom-toast" id="example-toast" style="position: absolute; top: 10px; right: 10px;"
        data-bs-delay="2000">
        <div class="toast-header">
            <i class="fa fa-bell"></i>
            <strong class="mr-auto">Notification</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <i class="fa fa-info-circle"></i> Hello, world! This is a beautifully styled toast message.
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary add_question" type="button">
        <i class="la la-plus"></i>@lang('Add New Question')
    </button>
@endpush

@push('style')
    <style>
        /* Custom Toast Style */
        .toast.custom-toast {
            background-color: #343a40;
            /* Dark background */
            color: #fff;
            /* White text */
            border-radius: 10px;
            /* Rounded corners */
            border: 2px solid #007bff;
            /* Blue border */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            /* Shadow */
            overflow: hidden;
        }

        .toast.custom-toast .toast-header {
            background-color: #007bff;
            /* Blue header */
            color: #fff;
            border-bottom: 1px solid #0056b3;
            /* Darker border at the bottom */
            padding: 10px;
        }

        .toast.custom-toast .toast-header strong {
            font-size: 16px;
            margin-right: auto;
        }

        .toast.custom-toast .toast-header small {
            font-size: 12px;
            color: #fff;
        }

        .toast.custom-toast .toast-header .btn-close {
            color: #fff;
            opacity: 1;
            font-size: 18px;
        }

        .toast.custom-toast .toast-body {
            font-size: 14px;
            padding: 20px;
            text-align: center;
            /* Center text */
        }

        .toast.custom-toast .toast-body i {
            margin-right: 10px;
            font-size: 20px;
            /* Larger icon */
        }
    </style>
@endpush

@push('script')
    <script>
        'use strict';
        // for test
        var task = @json($task);
        console.log(task);

        // Add New Question
        $('.add_question').on('click', function () {
            // For test
            console.log('Add new Question');

            var modal = $('#add_question');
            modal.find('input[name=id]').val(task['id']);
            modal.modal('show');
        });

        // Edit Existing Question
        $('.edit_question').on('click', function () {
            // For test
            console.log('Edit existing Question');

            var modal = $('#edit_question');
            modal.find('input[name=id]').val($(this).data('question_id'));
            modal.find('#edit_question_area').val($(this).data('question'));
            modal.modal('show');
        });

        // Delete Existing Question
        $('.delete_question').on('click', function () {
            // For test
            console.log('Delete existing Question');

            var modal = $('#delete_question');
            modal.find('input[name=id]').val($(this).data('question_id'));
            modal.find('#delete_question_area').val($(this).data('question'));
            modal.modal('show');
        });

        // Manage Answers
        $('.manage_answers').on('click', function () {
            // For test
            console.log('Managing Answers');

            var modal = $('#manage_answers');
            modal.find('input[name=id]').val($(this).data('question_id'));
            modal.find('#question_answers_area').val($(this).data('question'));
            modal.find('#add_answer_btn').attr('data-question_id', $(this).data('question_id'));
            var answers_data = $(this).data('answers');
            console.log('answers: ', answers_data);

            init_answers_area_with_data(answers_data);
            modal.modal('show');
        });

        $('#manage_answers_btn').on('click', function(e){
            e.preventDefault();
            // For test
            console.log('Manage answers count refresh');
            location.reload();
        })

        var answers_count = 0;
        var answers_global_data = [];
        var global_edit_answer_index = 0;
        var global_delete_answer_index = 0;

        function init_answers_global_data(answers) {
            answers_global_data = [];
            for (i = 0; i < answers_count; i++) {
                answers_global_data[i] = { 'id' : answers[i]['id'], 'answer': answers[i]['answer'], 'true_false': answers[i]['true_false'] };
            }
        }

        function init_answers_area_with_data(answers) {
            answers_count = answers.length;
            init_answers_global_data(answers);
            update_answers_area_with_global_data();
        }

        // check current answers are empty
        function check_answers_empty() {
            var flag = 0;
            for (i = 0; i < answers_count; i++) {
                var check_answer = $(`#answer${i}`).val();
                if (check_answer === null || $.trim(check_answer) === '') {
                    return false;
                }
            }
            return true;
        }

        // "Add new Answer" button click event
        $('.add_answer').on('click', function (e) {
            // For test
            console.log('Add new Answer');

            e.preventDefault();

            var modal = $('#add_new_answer_modal');
            modal.find('input[name=id]').val($(this).data('question_id'));
            modal.find('#new_answer_area').val('');
            modal.modal('show');
        });

        // Update Global Answers data
        function update_answers_global_data() {
            // For test
            console.log('Update Answers Global Data');
            for (i = 0; i < answers_count; i++) {
                var temp_answer = $(`#answer${i}`).val();
                var temp_true_false_var = $(`input[name='flexRadioDefault${i}']:checked`).val();
                var temp_true_false = +temp_true_false_var;
                answers_global_data[i] = { 'answer': temp_answer, 'true_false': temp_true_false };
            }
            console.log(answers_global_data);
        }

        // Update Answers Area with Global Answers data
        function update_answers_area_with_global_data() {
            // For test
            console.log('Update Answers Area with Global Answers Data', '   count of current answers : ', answers_count);
            console.log('Answers Global Data: ', answers_global_data);

            var current_answer_area = ``;
            $('#answers_area').html(``);
            for (i = 0; i < answers_count; i++) {
                current_answer_area = `<tr>
                                            <td colspan="5">${answers_global_data[i]['answer']}</td>
                                            <td id="true_false_result${i}"></td>
                                            <td>
                                                <button class="btn" style="color:green; padding:0" data-answer = "${answers_global_data[i]['answer']}" data-answer_id = "${answers_global_data[i]['id']}" onclick = "edit_answer_model_open(event, ${i})"><i class="fa fa-pen-fancy"
                                                        style="margin-right: 0;"></i></button>
                                                <button class="btn" style="color:red; padding:0" data-answer_id = "${answers_global_data[i]['id']}" onclick = "delete_answer_model_open(event, ${i})"><i class="fa fa-trash"
                                                        style="margin-right: 0;"></i></button>
                                            </td>
                                        </tr>`;
                $('#answers_area').append(current_answer_area);
                if (answers_global_data[i]['true_false']) {
                    $(`#true_false_result${i}`).text('True');
                } else {
                    $(`#true_false_result${i}`).text('False');
                }
            }
        }

        // Edit answer model opening
        function edit_answer_model_open(event, index){
            event.preventDefault();
            var modal = $('#edit_answer_modal');
            modal.find('#edit_answer_id').val(answers_global_data[index]['id']);
            modal.find('#edit_answer_area').val(answers_global_data[index]['answer']);
            modal.find('#edit_select_true_false').val(answers_global_data[index]['true_false']);
            global_edit_answer_index = index;
            modal.modal('show');
        }

        function delete_answer_model_open(event, index){
            event.preventDefault();
            var modal = $('#delete_answer_modal');
            modal.find('#delete_answer_id').val(answers_global_data[index]['id']);
            modal.find('#delete_answer_area').val(answers_global_data[index]['answer']);
            global_delete_answer_index = index;
            modal.modal('show');
        }

        // Add new answer by jquery ajax
        $('#add_new_answer_btn').on('click', function (e) {
            e.preventDefault();
            console.log('Add new Answer');

            var question_id = $('#new_answer_modal_question_id').val();
            var new_answer = $('#new_answer_area').val();
            var new_answer_true_false = +$('#new_select_true_false').val();
            var token = '{{ csrf_token() }}';

            var data = {
                question_id: question_id,
                answer: new_answer,
                true_false: new_answer_true_false,
                _token: token
            };

            $.post("{{route('admin.task.add_new_answer')}}", data, function (response) {
                console.log(response['message']);
                $('#add_new_answer_modal').modal('hide');
                $('#example-toast').toast('show');
                answers_global_data[answers_count] = {id : response['answer_id'], answer : new_answer, true_false : new_answer_true_false};
                answers_count++;
                update_answers_area_with_global_data();
            });
        });

        // edit answer by jquery ajax
        $('#update_answer_btn').on('click', function (e) {
            e.preventDefault();
            console.log('Update Answer');

            var answer_id = $('#edit_answer_id').val();
            var edit_answer = $('#edit_answer_area').val();
            var edit_answer_true_false = +$('#edit_select_true_false').val();
            var token = '{{ csrf_token() }}';

            var data = {
                answer_id: answer_id,
                answer: edit_answer,
                true_false: edit_answer_true_false,
                _token: token
            };

            $.post("{{route('admin.task.edit_answer')}}", data, function (response) {
                console.log(response['message']);
                $('#edit_answer_modal').modal('hide');
                $('#example-toast').toast('show');
                answers_global_data[global_edit_answer_index] = {id : answer_id, answer : edit_answer, true_false : edit_answer_true_false};
                update_answers_area_with_global_data();
            });
        });

        // delete answer by jquery ajax
        $('#delete_answer_btn').on('click', function (e) {
            e.preventDefault();
            console.log('Delete Answer');

            var answer_id = $('#delete_answer_id').val();
            var token = '{{ csrf_token() }}';

            var data = {
                answer_id: answer_id,
                _token: token
            };

            $.post("{{route('admin.task.delete_answer')}}", data, function (response) {
                console.log(response['message']);
                $('#delete_answer_modal').modal('hide');
                $('#example-toast').toast('show');
                answers_global_data.splice(global_delete_answer_index, 1);
                answers_count--;
                update_answers_area_with_global_data();
            });
        });

    </script>
@endpush