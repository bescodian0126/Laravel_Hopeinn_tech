@extends($activeTemplate . 'layouts.master')
@section('head')
@endsection
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">{{ __($pageTitle) }}</h3>
        </div>

        <div class="spinner-container" style="display:none">
            <div id="loading_spinner"  class="spinner-border" >
                <!-- Your spinner content here, e.g., an animated GIF or SVG -->
            </div>
        </div>
        {{-- <div id = "loading_spinner" class="spinner-border" style="display:none"></div> --}}

        <div class="row" id = "quiz_content">
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="card custom--card" style="margin-bottom:5px;">
                    <div class="card-header">
                        <h5>@lang('Notice')</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Try task! <br>
                            1. Click on <strong>Start Playing</strong> button. <br>
                            2. Then watch carefully the video. <br>
                            3. If you fully understand about the video, click <strong>START</strong> button to start
                            answering. <br>
                            4. If you are all correct answers, you will be received award money! <br>
                            5. We are not responsible if you don't correct for all answers!
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" id="downloadButton" class="btn btn-primary btn--sm form-control mb-1">Start
                            Playing</button>
                    </div>
                </div>

                <!-- Video Player -->
                <div class="video-player">
                    <video id="video_player" width="100%" height="100%" controls>
                        <source id="video_source" src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                <div id="discounter">
                    <div id="question_content_area" class="fs-3"></div>
                    <div id="timer-group">
                        <div id="timing-label">Timing</div>
                        <div class="countdown fs-3 text--danger">100</div>
                    </div>
                </div>

                <div class="row" id="start_quiz_area" style="width:100%">
                    <button id="start_quiz_btn" class="btn" style="font-size:40px; color:blue" disabled><img
                            src="{{ asset($activeTemplateTrue . 'users/images/icon/start2.png') }}"
                            style="width:70px; height:70px;" />START</button>

                </div>
                <div class="row" id="question_answers_area">

                </div>
            </div>


        </div>
    </div>

    <!-- ConfirmationModal -->
    <div class="modal fade" id="confirmationModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you ready to start?')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form>
                    @csrf
                    <div class="modal-body">
                        <p class="question"></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal"
                            type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm btn--primary" id="play_video_btn"
                            type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- All question matched or failed modal -->
    <div class="modal fade" id="quiz_success_modal" role="dialog" tabindex="-1" style="display:none">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="row">
                        <div class="col-12" style="justify-content:center; text-align:center;">
                            <img id="win_failed_image" style="width:100px; heigth: auto;" />
                        </div>
                        <div class="col-12">
                            <div class="row" style="justify-content:center; text-align:center">
                                <h4 class="modal-title quiz_result_header"></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <form>
                    @csrf
                    <div class="modal-body" style="justify-content:center; text-align:center">
                        <p class="quiz_result_text"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .spinner-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            /* Optional: background overlay */
            z-index: 1000;
            /* Ensure it's above other content */
        }

        #loading_spinner {
            width: 20rem;
            height: 20rem;
            background: transparent;
            /* Add any additional styles for the spinner */
        }

        #discounter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            display: none;
        }

        #timer-group {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #timing-label {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .countdown {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e74c3c;
            padding-bottom: 10px;
            text-align: center;
            animation: sparkling 1s ease-in-out infinite;
        }

        @keyframes sparkling {

            0%,
            100% {
                font-size: 2.5rem;
            }

            50% {
                font-size: 2.7rem;
                /* Adjusted keyframe to make the animation more visible */
            }
        }

        #question_content_area {
            flex: 1;
            text-align: center;
            font-size: 1.8rem;
            color: #333;
            margin-right: 10px;
        }

        div.btn-answer {
            transition: width 0.1s ease;
        }

        div.btn-answer:hover {
            width: 101%;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>


    <script>
        'use strict';

        var global_true_false = 1;
        var redirect_url = "{{ route('user.task.index') }}";

        // var redirectFlag = Cookies.get('redirectAfterRefresh');
        var initial_time = 99;
        // if (redirectFlag !== undefined) {
        //     // Remove the cookie (optional)
        //     Cookies.remove('redirectAfterRefresh');

        //     // Perform redirection
        //     window.location.href = redirect_url;
        // }
        $(document).ready(function() {
            // Check if the page was loaded before
            // if (sessionStorage.getItem('isRefreshed')) {
            //     console.log('Page was refreshed');
            // } else {
            //     sessionStorage.setItem('isRefreshed', 'true');
            //     console.log('First time page load');
            // }

            $(window).on('beforeunload', function() {
                // Optionally clear the session storage or perform other actions before the page unloads
            });

            $('#quiz_success_modal').on('hidden.bs.modal', function() {
                console.log('Modal is hidden');
                window.location.href = redirect_url;
            });



            const $countdown = $('.countdown');
            let timerInterval; // Declare timerInterval globally

            function formatTime(seconds) {
                return seconds < 10 ? '0' + seconds : seconds;
            }

            window.start_downcount = function start_downcount() {
                let remainingTime = 100;

                // Clear existing timer interval before starting a new one
                if (timerInterval) {
                    clearInterval(timerInterval);
                }

                // Immediately update the countdown display
                $countdown.text(formatTime(remainingTime));

                timerInterval = setInterval(function() {
                    remainingTime--;

                    $countdown.text(formatTime(remainingTime));

                    if (remainingTime <= 0) {
                        clearInterval(timerInterval);
                        $countdown.text('00');
                        var isHidden = $('#quiz_success_modal').css('display') === 'none';
                        if (isHidden) {
                            console.log('Modal is already hidden');
                            send_task_result(0);
                        }
                    }
                }, 1000);
            };

            // Example function call (not in your provided code but useful to test)
            // window.start_downcount();
        });

        var countdown = $('.countdown');

        function check_answer(index, i) {
            var true_false_temp = questions_global_data[index]['answers'][i]['true_false'];
            if (true_false_temp == 0) global_true_false = 0;
            if (index == questions_global_data.length - 1) {
                if (global_true_false == 1) {
                    send_task_result(1);
                } else {
                    send_task_result(0);
                }
            } else {
                generate_answers_area_with_index(index + 1);
                generate_question_number_area_with_index(index + 1);
                if (true_false_temp == 0) {
                    global_true_false = true_false_temp;
                }
                window.start_downcount(); // Call globally accessible start_downcount
            }
        }


        function send_task_result(result) {
            var token = "{{ csrf_token() }}";

            var data = {
                result: result,
                task_status_id: task_status_id,
                user_id: user_id,
                task_id: task_id,
                _token: token
            }

            $.post("{{ route('user.task.check_result') }}", data, function(response) {
                redirect_url = response.redirect_url;
                // console.log(response.redirect_url);
                // Cookies.set('redirectAfterRefresh', 'true', {
                //     expires: 1
                // });
                console.log('localStorage is setted');
                if (response.message === 'success') {
                    // console.log(response);
                    var modal = $('#quiz_success_modal');
                    modal.find('.quiz_result_header').text('Congratlations! Your answsers are all correct!');
                    modal.find('.quiz_result_text').text('You will receive money!');
                    $('#win_failed_image').attr('src',
                        "{{ asset($activeTemplateTrue . 'users/images/icon/win.png') }}");
                    modal.modal('show');
                } else {
                    // console.log(response);
                    var modal = $('#quiz_success_modal');
                    modal.find('.quiz_result_header').text('Failed! Your answsers are not correct!');
                    modal.find('.quiz_result_text').text('Do you mind to start again?');
                    $('#win_failed_image').attr('src',
                        "{{ asset($activeTemplateTrue . 'users/images/icon/failed.png') }}");
                    modal.modal('show');

                }
            })
        }

        var video_status = 'STOP';

        var user_id = @json($user_id);
        var status = @json($status);
        var task_id = @json($task_id);
        var task_status_id = @json($task_status_id);
        var questions;
        // console.log('user_id : ', user_id, '    task_id : ', task_id, '      status : ', status);
        var prevent_refresh_flag = 0;

        var questions_global_data = [];
        var current_question_index = 0;
        var video_player = document.getElementById('video_player');


        $('#downloadButton').on('click', function(e) {
            e.preventDefault();

            /*  */
            $('.spinner-container').show();

            var data = {
                task_status_id: task_status_id,
                task_id: {{ $task_id }},
                user_id: user_id,
                flag: prevent_refresh_flag,
                _token: "{{ csrf_token() }}"
            };

            


            $.post("{{ route('user.task.download_video') }}", data, function(response) {
                var task_status = response.task_status;
                console.log(task_status);
                if (task_status == 5 || task_status == 2) {
                    alert('You already purchased this task');
                } else {
                    if (response.message == 'failed') {
                        $('#start_quiz_btn').prop('disabled', true);
                        alert('You already started this task!');
                    } else {
                        var file_type = response.file_type;

                        if (file_type == 'tiktok_video') {
                            var tiktok_url = response.file_name;
                            var windowFeatures = "width=800,height=600,left=100,top=100";
                            $('#start_quiz_btn').prop('disabled', false);
                            init_question_answers(response.questions);
                            window.open(tiktok_url, "TikTokWindow", windowFeatures);
                        } else {
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            // Create a blob from the base64-encoded file content
                            var binary = atob(response.file_content);
                            var array = [];
                            for (var i = 0; i < binary.length; i++) {
                                array.push(binary.charCodeAt(i));
                            }
                            var blob = new Blob([new Uint8Array(array)], {
                                type: response.file_type
                            });
                            var videoUrl = URL.createObjectURL(blob);

                            // Set the video source and play the video
                            $('#video_source').attr('src', videoUrl);
                            $('#quiz_content').hide();

                            
                            video_player.load();

                            var modal = $('#confirmationModal');
                            let data = $(this).data();
                            modal.find('.question').text('If you want to start, press Yes');
                            $('#quiz_content').show();
                            $('.spinner-container').hide();
                            modal.modal('show');
                            video_player.controls = false;
                            init_question_answers(response.questions);
                        }
                    }
                }
            });
        });

        function init_question_answers(questions) {
            questions_global_data = questions;
            init_question_answers_area_with_global_data();
        }

        function init_question_answers_area_with_global_data() {
            $('#question_answers_area').html(``);
            $('#discounter').hide();
            $('#question_answers_area').hide();

            generate_answers_area_with_index(0);
            generate_question_number_area_with_index(0);

            current_question_index = 0;
        }

        function generate_answers_area_with_index(index) {
            var question_answers_area_content = `<div class="col-12">`;
            var i;
            for (i = 0; i < questions_global_data[index]['answers'].length; i++) {
                question_answers_area_content = question_answers_area_content + `
                                                                <div class="btn btn-primary form form-control mb-1 btn-sm btn-answer" onclick="check_answer(${index}, ${i})">${questions_global_data[index]['answers'][i]['answer']}</div>
                                                            `;
            }
            question_answers_area_content = question_answers_area_content + `</div>`;
            $('#question_answers_area').html(question_answers_area_content);

            generate_question_area_with_index(index);
        }




        function generate_question_area_with_index(index) {
            $('#question_content_area').html(questions_global_data[index]['question']);
        }

        function generate_question_number_area_with_index(index) {
            var question_number_area_content = `${current_question_index + 1} of ${questions_global_data.length}`
            $('#question_number_area').html(question_number_area_content);
        }

        $('#play_video_btn').on('click', function(e) {
            e.preventDefault();
            var modal = $('#confirmationModal');
            modal.modal('hide');
            video_player.play();
            $('#start_quiz_btn').prop('disabled', false);
        })

        $(video_player).on('error', function() {
            console.error('Video playback error:', video.error);
            // Handle error: Display error message, retry, or show alternative content
        });

        $('#start_quiz_btn').on('click', function() {
            $(this).hide();
            $('#question_answers_area').show();
            $('#discounter').css("display", "flex");
            window.start_downcount();
            generate_answers_area_with_index(current_question_index);
            generate_question_number_area_with_index(current_question_index);
        })


        $('.confirmationBtn').on('click', function() {
            var modal = $('#confirmationModal');
            let data = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });

        video_player.addEventListener('ended', function() {
            set_video_state('ENDED');
            // console.log(video_status);
        });

        function set_video_state(state) {
            video_status = state;
        }
    </script>
@endpush
