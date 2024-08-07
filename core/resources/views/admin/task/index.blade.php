@extends('admin.layouts.app')

@section('panel')
<div class="col-lg-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table--light style--two table">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Required Energy')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Reward')</th>
                            <th>@lang('Quiz Video File Name/Path')</th>
                            <th>@lang('Number of questions')</th>
                            <th>@lang('Status')</th>
                            <th style="text-align : center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $key => $task)
                            <tr>
                                <td>{{ __($task->task_name) }}</td>
                                <td>{{ showAmount($task->energy_cost) }} {{ __($general->cur_text) }}</td>

                                <td>
                                    {{ $task->description }}
                                </td>
                                <td>
                                    {{ $task->reward }}{{ __($general->cur_text) }}
                                </td>
                                <td>
                                    {{ $task->file_name == '' ? $task->net_video_url : $task->file_name}}
                                </td>
                                <td>
                                    {{ !empty($task->taskQuizzes) ? count($task->taskQuizzes) : 0}}
                                </td>
                                <td>
                                    @php echo $task->statusBadge @endphp
                                </td>

                                <td style="text-align : center">

                                    @if($task->video_path_type == 0 || $task->video_path_type == 1)
                                    <a class="btn btn-sm btn-outline--success add_questions" data-toggle="tooltip"
                                        data-id="{{$task->id}}" data-type="button"
                                        href="{{route('admin.task.add_questions', ['id' => $task->id])}}">
                                        <i class="la la-pencil"></i>@lang('Manage Questions')
                                    </a>
                                    @endif

                                    <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip"
                                        title="Edit Task" data-id="{{$task->id}}" data-name="{{$task->task_name}}"
                                        data-required_energy_ctrl="{{$task->energy_cost}}"
                                        data-reward_energy_ctrl="{{$task->reward}}"
                                        data-daily_duration="{{$task->daily_duration}}"
                                        data-invite_counts="{{$task->invite_counts}}"
                                        data-invite_duration="{{$task->invite_duration}}"
                                        data-video_path_type="{{$task->video_path_type}}"
                                        data-net_video_url="{{$task->net_video_url}}"
                                        data-description="{{$task->description}}" data-quizzes="@json($task->task_quizzes)"
                                        data-index="{{$loop->index}}" data-file_name="{{$task->file_name}}" data-selected_plan="{{$task->plan_id}}" data-bonus_energy="{{$task->bonus_energy}}" data-bonus_depth="{{$task->bonus_depth}}"
                                        type="button">
                                        <i class="la la-pencil"></i>@lang('Edit')
                                    </button>

                                    @if ($task->status == 0)
                                        <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                            data-question="@lang('Are you sure to enable this task?')"
                                            data-action="{{ route('admin.task.status', $task->id) }}">
                                            <i class="la la-eye"></i> @lang('Enable')
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                            data-question="@lang('Are you sure to disable this task?')"
                                            data-action="{{ route('admin.task.status', $task->id) }}">
                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                        </button>
                                    @endif
                                    <!--<button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"-->
                                    <!--    data-question="@lang('Are you sure to enable this task?')"-->
                                    <!--    data-action="{{ route('admin.task.delete', $task->id) }}">-->
                                    <!--    <i class="las la-trash"></i> @lang('Delete')-->
                                    <!--</button>-->
                                    

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

<!-- Edit Task Modal -->
<div class="modal fade" id="edit-task" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit task')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.task.save') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <input class="form-control task_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>@lang('Name')</label>
                            <input class="form-control name" name="name" type="text" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Required Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control required_energy_ctrl" name="required_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Reward Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control reward_energy_ctrl" name="reward_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>@lang('Select Plan') </label>
                            <div class="input-group">
                                <select class="form-select" name="select_plan" id="select_plan_edit">
                                    <option value = "">@lang('Select One')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{$plan->id}}">{{$plan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>@lang('Bonus Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control bonus_energy_ctrl_edit" name="bonus_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>@lang('Depth') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control bonus_depth_ctrl_edit" name="bonus_depth" type="number"
                                    step="any" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Quiz Video from local')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn_edit" value="local"
                                    name="radio_btn_edit" style="cursor:pointer" checked>
                            </div>
                            <input type="file" name="taskVideoFile" id="taskVideoFile_edit"
                                class="form-control form--control ms-2"
                                accept=".mp4, .avi, .mkv, .mov, .wmv, .flv, .webm, .mpeg, .mpg" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Quiz Video from Tiktok')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn1_edit" value="net_video_url"
                                    name="radio_btn_edit" style="cursor:pointer">
                            </div>
                            <input type="text" name="net_video_url" id="net_video_url_edit"
                                class="form-control form--control ms-2" required>
                        </div>
                    </div>
                    <!-- Daily Login tasks -->
                    <div class="form-group mb-3">
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn2_edit" value="daily_duration"
                                    name="radio_btn_edit" style="cursor:pointer">
                            </div>
                            <label for="radio_btn2_edit" class="form-control form--control ms-2"
                                style="border:none">@lang('Daily login tasks')
                            </label>
                            <input type="text" class="form-control form--control ms-2" name="daily_duration"
                                id="daily_duration_edit" placeholder="Your input here" required>
                        </div>
                    </div>
                    <!-- Invite Friends -->
                    <div class="form-group mb-3">
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn3_edit" value="invite_friends"
                                    name="radio_btn_edit" style="cursor:pointer">
                            </div>
                            <label for="radio_btn3_edit" class="form-control form--control ms-2"
                                style="border:none">@lang('Invite Friends')
                            </label>
                            <input name="invite_counts" id="invite_counts_edit" type="text"
                                class="form-control form--control ms-2" placeholder="Number of invite friends" required>
                            <label for="invite_duration_edit" class="form-control form--control ms-2"
                                style="border:none">@lang('Duration')
                            </label>
                            <input type="text" name="invite_duration" id="invite_duration_edit"
                                class="form-control form--control ms-2" placeholder="Duration" required>

                        </div>
                    </div>
                    <label>@lang('Description') </label>
                    <textarea name="description" maxlength="255" class="form-control mb-4 description" rows="2"
                        required></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="add-task" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New task')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.task.save') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input class="form-control task_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>@lang('Name')</label>
                            <input class="form-control name" name="name" type="text" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Required Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control required_energy_ctrl" name="required_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Reward Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control reward_energy_ctrl" name="reward_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>@lang('Select Plan') </label>
                            <div class="input-group">
                                <select class="form-select" name="select_plan" id="select_plan">
                                    <option value = "">@lang('Select One')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{$plan->id}}">{{$plan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>@lang('Bonus Energy') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control bonus_energy_ctrl" name="bonus_energy" type="number"
                                    step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>@lang('Depth') </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control bonus_depth_ctrl" name="bonus_depth" type="number"
                                    step="any" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Quiz Video from local')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn" value="local"
                                    name="radio_option" style="cursor:pointer" checked>
                            </div>
                            <input type="file" name="taskVideoFile" id="taskVideoFile"
                                class="form-control form--control ms-2"
                                accept=".mp4, .avi, .mkv, .mov, .wmv, .flv, .webm, .mpeg, .mpg" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Quiz Video from Tiktok')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn1" value="net_video_url"
                                    name="radio_option" style="cursor:pointer">
                            </div>
                            <input type="text" name="net_video_url" id="net_video_url"
                                class="form-control form--control ms-2" required>
                        </div>
                    </div>
                    <!-- Daily Login tasks -->
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Daily login tasks')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn2" value="daily_duration"
                                    name="radio_option" style="cursor:pointer">
                            </div>
                            <label for="radio_btn2" class="form-control form--control ms-2"
                                style="border:none">@lang('Daily login tasks')
                            </label>
                            <input type="text" class="form-control form--control ms-2" name="daily_duration"
                                id="daily_duration" placeholder="Your input here" required>
                        </div>
                    </div>
                    <!-- Invite Friends -->
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">@lang('Invite Friends')</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="radio_btn3" value="invite_friends"
                                    name="radio_option" style="cursor:pointer">
                            </div>
                            <label for="radio_btn3" class="form-control form--control ms-2"
                                style="border:none">@lang('Invite Friends')
                            </label>
                            <input name="invite_counts" id="invite_counts" type="text"
                                class="form-control form--control ms-2" placeholder="Number of invite friends" required>
                            <label for="invite_duration" class="form-control form--control ms-2"
                                style="border:none">@lang('Duration')
                            </label>
                            <input type="text" name="invite_duration" id="invite_duration"
                                class="form-control form--control ms-2" placeholder="Duration" required>

                        </div>
                    </div>
                    <label>@lang('Description') </label>
                    <textarea name="description" maxlength="255" class="form-control mb-4 description" rows="2"
                        required></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ConfirmationModal -->
<div class="modal fade" id="confirmationModal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <button class="btn btn--base btn--sm btn--primary" type="submit">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary add-task" type="button">
        <i class="la la-plus"></i>@lang('Add New Task')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        $('.add-task').on('click', function () {
            var modal = $('#add-task');
            modal.modal('show');
        });

        var tasks = @json($tasks);
        console.log(tasks);


        var quiz_index = 0;
        var quiz_content = [];
        var questions = [];
        var answers = [];
        var correct_answers = [];

        function generate_quiz_content_by_index(index) {
            quiz_content[index] = `
                                                <div class="col-md-6 mb-3">
                                                    <label>Question</label>
                                                    <textarea name="question[]" id="question${index}" maxlength="255" class="form-control" rows="2" placeholder="Place your question" required></textarea>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label>Answers</label>
                                                    <textarea name="answers[]" id="answers${index}" class="form-control" rows="2" placeholder="" required></textarea>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label>Correct Answer</label>
                                                    <textarea name="correct_answer[]" id="correct_answer${index}" class="form-control" rows="2" required></textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <button class="form-control btn btn-danger btn_remove_question_answer" data-index="${index}">Cancel</button>
                                                </div>
                                            `;
        }



        $('#add_questions').on('click', function (e) {
            e.preventDefault();

            // for (i = 0; i <= quiz_index; i++) {
            //     $(`#question${i}`).val(questions[i] || '');
            //     $(`#answers${i}`).val(answers[i] || '');
            //     $(`#correct_answer${i}`).val(correct_answers[i] || '');
            // }

            if (quiz_index == 0) {
                generate_quiz_content_by_index(0);
                $('#question_answer').append(quiz_content[0]);
                // Increment the quiz index for the next question
                quiz_index++;
                console.log(quiz_index, questions, answers, correct_answers);
            }
            else {
                for (i = 0; i < quiz_index; i++) {
                    var index_question = $(`#question${i - 1}`).val();
                    var index_answers = $(`#answers${i - 1}`).val();
                    var index_correct_answer = $(`#correct_answer${i - 1}`).val();
                    console.log('iteration index : ', i - 1);
                    console.log(index_question, index_answers, index_correct_answer);
                    if (index_question == '' || index_answers == '' || index_correct_answer == '') {
                        alert('All fields must be filled.');

                    } else {
                        questions[i] = index_question;
                        answers[i] = index_answers;
                        correct_answers[i] = index_correct_answer;
                        generate_quiz_content_by_index(quiz_index);
                        $('#question_answer').append(quiz_content[quiz_index]);
                        // Increment the quiz index for the next question
                        quiz_index++;
                        console.log(quiz_index, questions, answers, correct_answers);
                    }
                }
            }

        });

        // Delegate event from a static parent element to the dynamically added buttons
        $('#question_answer').on('click', '.btn_add_question_answer', function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            btn_add_question_answer(index, e);
        });

        $('#question_answer').on('click', '.btn_edit_question_answer', function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            btn_edit_question_answer(index, e);
        });

        $('#question_answer').on('click', '.btn_remove_question_answer', function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            btn_remove_question_answer(index, e);
        });

        function btn_add_question_answer(index, e) {
            e.preventDefault();
            var index_question = $(`#question${index}`).val();
            var index_answers = $(`#answers${index}`).val();
            var index_correct_answer = $(`#correct_answer${index}`).val();
            if (index_question == '' || index_answers == '' || index_correct_answer == '') {
                alert('All fields must be filled.');
            } else {
                questions[index] = index_question;
                answers[index] = index_answers;
                correct_answers[index] = index_correct_answer;
                $(`.btn_add_question_answer[data-index="${index}"]`).prop('disabled', true);
                $(`.btn_edit_question_answer[data-index="${index}"]`).prop('disabled', false);
                $(`.btn_remove_question_answer[data-index="${index}"]`).prop('disabled', false);

                // Enable other buttons in the same row
                // $row.find('.btn_edit_question_answer, .btn_remove_question_answer').prop('disabled', false);
            }
            console.log(index, questions, answers, correct_answers, quiz_content);
        }

        function btn_edit_question_answer(index, e) {
            e.preventDefault();
            var index_question = $(`#question${index}`).val();
            var index_answers = $(`#answers${index}`).val();
            var index_correct_answer = $(`#correct_answer${index}`).val();
            if (index_question == '' || index_answers == '' || index_correct_answer == '') {
                alert('All fields must be filled.');
            } else {
                questions[index] = index_question;
                answers[index] = index_answers;
                correct_answers[index] = index_correct_answer;
                $(`.btn_add_question_answer[data-index="${index}"]`).prop('disabled', true);
                $(`.btn_edit_question_answer[data-index="${index}"]`).prop('disabled', false);
                $(`.btn_remove_question_answer[data-index="${index}"]`).prop('disabled', false);

                // Enable other buttons in the same row
                // $row.find('.btn_edit_question_answer, .btn_remove_question_answer').prop('disabled', false);
            }
            console.log(index, questions, answers, correct_answers, quiz_content);
        }

        function btn_remove_question_answer(index, e) {
            e.preventDefault();
            // Implement your cancel logic here
            quiz_content.splice(index, 1);
            questions.splice(index, 1);
            answers.splice(index, 1);
            correct_answers.splice(index, 1);

            // Remove the HTML element from the DOM

            // Adjust quiz_index if needed (if removing from the middle)
            quiz_index--;
            $(`#question_answer`).html(``);

            for (i = 0; i < quiz_index; i++) {
                generate_quiz_content_by_index(i);
                $(`#question_answer`).append(quiz_content[i]);
                $(`#question${i}`).val(questions[i] || '');
                $(`#answers${i}`).val(answers[i] || '');
                $(`#correct_answer${i}`).val(correct_answers[i] || '');
            }

            console.log('removed', quiz_index, index, questions, answers, correct_answers, quiz_content);

            // Adjust quiz_index if needed (if removing from the middle)
        }


        // Edit Modal

        function generate_edit_modal_quizzes_by_index(quiz, index) {
            console.log(index, quiz);
            var quiz_content = `
                                                <div class="col-md-6 mb-3">
                                                    <label>Question</label>
                                                    <textarea name="question[]" id="question${index}" maxlength="255" class="form-control" rows="2" placeholder="Place your question" required>${quiz['question']}</textarea>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label>Answers</label>
                                                    <textarea name="answers[]" id="answers${index}" class="form-control" rows="2" placeholder="" required>${quiz['answers']}</textarea>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label>Correct Answer</label>
                                                    <textarea name="correct_answer[]" id="correct_answer${index}" class="form-control" rows="2" required>${quiz['correct_answer']}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="form-control btn btn-success btn_add_question_answer" data-index="${index}">Add</button>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="form-control btn btn-primary btn_edit_question_answer" data-index="${index}">Edit</button>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="form-control btn btn-secondary btn_remove_question_answer" data-index="${index}">Cancel</button>
                                                </div>
                                            `;
            $('#edit_question_answer').append(quiz_content);

        }

        function generate_edit_modal_quizzes(quizzes) {
            // console.log(quizzes.length);
            for (i = 0; i < quizzes.length; i++) {
                generate_edit_modal_quizzes_by_index(quizzes[i], i);
            }
        }

        $('.edit').on('click', function () {
            var modal = $('#edit-task');
            modal.find('.name').val($(this).data('name'));
            modal.find('.required_energy_ctrl').val($(this).data('required_energy_ctrl'));
            modal.find('.reward_energy_ctrl').val($(this).data('reward_energy_ctrl'));
            modal.find('#select_plan_edit').val($(this).data('selected_plan'));
            modal.find('.bonus_energy_ctrl_edit').val($(this).data('bonus_energy'));
            modal.find('.bonus_depth_ctrl_edit').val($(this).data('bonus_depth'));
            modal.find('#edit_taskVideoFile').val('');
            modal.find('#daily_duration_edit').val('');
            modal.find('#invite_counts').val('');
            modal.find('#invite_duration').val('');

            var video_path_type = $(this).data('video_path_type');
            if (video_path_type == 0) {
                modal.find('#taskVideoFile_edit').val('');
                modal.find('#net_video_url_edit').val('');
                modal.find('#daily_duration_edit').val('');
                modal.find('#invite_counts_edit').val('');
                modal.find('#invite_duration_edit').val('');
                modal.find('#taskVideoFile_edit').prop('disabled', false);
                modal.find('#net_video_url_edit').prop('disabled', true);
                modal.find('#daily_duration_edit').prop('disabled', true);
                modal.find('#invite_counts_edit').prop('disabled', true);
                modal.find('#invite_duration_edit').prop('disabled', true);
                modal.find('#radio_btn_edit').prop('checked', true);
                modal.find('#radio_btn1_edit').prop('checked', false);
                modal.find('#radio_btn2_edit').prop('checked', false);
                modal.find('#radio_btn3_edit').prop('checked', false);
            } else if (video_path_type == 1) {
                modal.find('#net_video_url_edit').val($(this).data('net_video_url'));
                modal.find('#daily_duration_edit').val('');
                modal.find('#invite_counts_edit').val('');
                modal.find('#invite_duration_edit').val('');
                modal.find('#taskVideoFile_edit').prop('disabled', true);
                modal.find('#net_video_url_edit').prop('disabled', false);
                modal.find('#daily_duration_edit').prop('disabled', true);
                modal.find('#invite_counts_edit').prop('disabled', true);
                modal.find('#invite_duration_edit').prop('disabled', true);
                modal.find('#radio_btn_edit').prop('checked', false);
                modal.find('#radio_btn1_edit').prop('checked', true);
                modal.find('#radio_btn2_edit').prop('checked', false);
                modal.find('#radio_btn3_edit').prop('checked', false);
            } else if (video_path_type == 2) {
                modal.find('#taskVideoFile_edit').val('');
                modal.find('#net_video_url_edit').val('');
                modal.find('#daily_duration_edit').val('');
                modal.find('#invite_counts_edit').val('');
                modal.find('#invite_duration_edit').val('');
                modal.find('#taskVideoFile_edit').prop('disabled', true);
                modal.find('#net_video_url_edit').prop('disabled', true);
                modal.find('#daily_duration_edit').prop('disabled', false);
                modal.find('#invite_counts_edit').prop('disabled', true);
                modal.find('#invite_duration_edit').prop('disabled', true);
                modal.find('#radio_btn_edit').prop('checked', false);
                modal.find('#radio_btn1_edit').prop('checked', false);
                modal.find('#radio_btn2_edit').prop('checked', true);
                modal.find('#radio_btn3_edit').prop('checked', false);
                modal.find('#daily_duration_edit').val($(this).data('daily_duration'));
                
            } else if (video_path_type == 3) {
                modal.find('#taskVideoFile_edit').val('');
                modal.find('#net_video_url_edit').val('');
                modal.find('#daily_duration_edit').val('');
                modal.find('#invite_counts_edit').val('');
                modal.find('#invite_duration_edit').val('');
                modal.find('#taskVideoFile_edit').prop('disabled', true);
                modal.find('#net_video_url_edit').prop('disabled', true);
                modal.find('#daily_duration_edit').prop('disabled', true);
                modal.find('#invite_counts_edit').prop('disabled', false);
                modal.find('#invite_duration_edit').prop('disabled', false);
                modal.find('#radio_btn_edit').prop('checked', false);
                modal.find('#radio_btn1_edit').prop('checked', false);
                modal.find('#radio_btn2_edit').prop('checked', false);
                modal.find('#radio_btn3_edit').prop('checked', true);
                modal.find('#invite_counts_edit').val($(this).data('invite_counts'));
                modal.find('#invite_duration_edit').val($(this).data('invite_duration'));
            }

            modal.find('.description').val($(this).data('description'));
            modal.find('input[name=id]').val($(this).data('id'));

            var tasks = @json($tasks);
            var index = $(this).data('index');
            var quizzes = tasks[index]['task_quizzes'];

            generate_edit_modal_quizzes(quizzes);

            modal.modal('show');


        });

        $('.confirmationBtn').on('click', function () {
            var modal = $('#confirmationModal');
            let data = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });

        $(document).ready(function () {
            // Initially disable the net_video_url input
            $('#net_video_url').prop('disabled', true);
            $('#daily_duration').prop('disabled', true);
            $('#invite_counts').prop('disabled', true);
            $('#invite_duration').prop('disabled', true);

            // Handle radio button change
            $('input[name="radio_option"]').change(function () {
                if ($('#radio_btn').is(':checked')) {
                    $('#taskVideoFile').prop('disabled', false);
                    $('#net_video_url').prop('disabled', true);
                    $('#daily_duration').prop('disabled', true);
                    $('#invite_counts').prop('disabled', true);
                    $('#invite_duration').prop('disabled', true);
                } else if ($('#radio_btn1').is(':checked')) {
                    $('#taskVideoFile').prop('disabled', true);
                    $('#net_video_url').prop('disabled', false);
                    $('#daily_duration').prop('disabled', true);
                    $('#invite_counts').prop('disabled', true);
                    $('#invite_duration').prop('disabled', true);
                } else if ($('#radio_btn2').is(':checked')) {
                    $('#taskVideoFile').prop('disabled', true);
                    $('#net_video_url').prop('disabled', true);
                    $('#daily_duration').prop('disabled', false);
                    $('#invite_counts').prop('disabled', true);
                    $('#invite_duration').prop('disabled', true);
                } else if ($('#radio_btn3').is(':checked')) {
                    $('#taskVideoFile').prop('disabled', true);
                    $('#net_video_url').prop('disabled', true);
                    $('#daily_duration').prop('disabled', true);
                    $('#invite_counts').prop('disabled', false);
                    $('#invite_duration').prop('disabled', false);
                }
            });
        });

        $(document).ready(function () {
            // Initially disable the net_video_url input
            // Handle radio button change
            $('input[name="radio_btn_edit"]').change(function () {
                if ($('#radio_btn_edit').is(':checked')) {
                    $('#taskVideoFile_edit').prop('disabled', false);
                    $('#net_video_url_edit').prop('disabled', true);
                    $('#daily_duration_edit').prop('disabled', true);
                    $('#invite_counts_edit').prop('disabled', true);
                    $('#invite_duration_edit').prop('disabled', true);
                } else if ($('#radio_btn1_edit').is(':checked')) {
                    $('#taskVideoFile_edit').prop('disabled', true);
                    $('#net_video_url_edit').prop('disabled', false);
                    $('#daily_duration_edit').prop('disabled', true);
                    $('#invite_counts_edit').prop('disabled', true);
                    $('#invite_duration_edit').prop('disabled', true);
                } else if ($('#radio_btn2_edit').is(':checked')) {
                    $('#taskVideoFile_edit').prop('disabled', true);
                    $('#net_video_url_edit').prop('disabled', true);
                    $('#daily_duration_edit').prop('disabled', false);
                    $('#invite_counts_edit').prop('disabled', true);
                    $('#invite_duration_edit').prop('disabled', true);
                } else if ($('#radio_btn3_edit').is(':checked')) {
                    $('#taskVideoFile_edit').prop('disabled', true);
                    $('#net_video_url_edit').prop('disabled', true);
                    $('#daily_duration_edit').prop('disabled', true);
                    $('#invite_counts_edit').prop('disabled', false);
                    $('#invite_duration_edit').prop('disabled', false);
                }

            });
        });

    </script>
@endpush