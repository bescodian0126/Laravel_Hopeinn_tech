@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">@lang('My Tree')</h3>
    </div>

    <div class="mb-4">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center">@lang('Referrer Link')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <form>
                            <div class="form-group">
                                <label class="form-label">@lang('Join left')</label>
                                <div class="copy-link">
                                    <input class="copyURL w-100" type="text"
                                        value="{{ route('home') }}/?ref={{ auth()->user()->username }}&position=left"
                                        readonly>
                                    <span class="copyBoard" id="copyBoard">
                                        <i class="las la-copy"></i>
                                        <strong class="copyText">@lang('Copy')</strong>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <form>
                            <div class="form-group">
                                <label class="form-label">@lang('Join Center')</label>
                                <div class="copy-link">
                                    <input class="copyURL3 w-100" type="text"
                                        value="{{ route('home') }}/?ref={{ auth()->user()->username }}&position=center"
                                        readonly>
                                    <span class="copyBoard3" id="copyBoard3">
                                        <i class="las la-copy"></i>
                                        <strong class="copyText3">@lang('Copy')</strong>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <form>
                            <div class="form-group">
                                <label class="form-label">@lang('Join right')</label>
                                <div class="copy-link">
                                    <input class="copyURL2 w-100" type="text"
                                        value="{{ route('home') }}/?ref={{ auth()->user()->username }}&position=right"
                                        readonly>
                                    <span class="copyBoard2" id="copyBoard2">
                                        <i class="las la-copy"></i>
                                        <strong class="copyText2">@lang('Copy')</strong>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="treePanel">

    </div>

    <div class="card custom--card mt-4 mb-4" id="treeAccordian" style="display : none">
        <div class="card-body">
            <div class="mt-3 treeAccordian">

            </div>
        </div>
    </div>


    <div class="modal fade user-details-modal-area" id="exampleModalCenter" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="@lang('Close')">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-details-modal">
                        <div class="user-details-header">
                            <div class="thumb"><img class="tree_image w-h-100-p" src="#" alt="*"></div>
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
                                    <td>@lang('Current BV')</td>
                                    <td><span class="lbv"></span></td>
                                    <td><span class="cbv"></span></td>
                                    <td><span class="rbv"></span></td>
                                </tr>
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
        <link href="{{ asset($activeTemplateTrue . 'users/css/tree.css') }}" rel="stylesheet">
        <style>
            .card-header {
                border: none;
            }

            .custom--card .card-header {
                padding-left: 40px;
                padding-right: 0px;
                padding-top: 15px;
                padding-bottom: 15px;
            }
        </style>
    @endpush
    @push('script')
        <script>
            "use strict";
            var toggler = document.getElementsByClassName("caret");
            function getNonNullSize(data) {
                if (typeof data !== 'object' || data === null) {
                    return 0;
                }

                let size = 0;
                for (let key in data) {
                    if (data[key] !== null) {
                        size++;
                    }
                }
                return size;
            }


            var treeData = @json($data);
            console.log(treeData);
            var i;
            var objectLengh = getNonNullSize(treeData);
            console.log(objectLengh);

            var entireCount = objectLengh;
            var imageSize = [100, 70, 50, 30, 20, 10];
            var fontSize = [30, 25, 20, 18, 15, 13];
            var memberFontSize = [20, 18, 17, 15, 13, 12];

            // console.log(constantData);
            // console.log(countData);

            var htmlContent = [];

            function recursive_tree(node, plan_num, depth, content) {
                if (depth == 6) return content;
                var pos = '';
                if(node.position == 1) pos = 'L';
                else if(node.position == 2) pos = 'C';
                else pos = 'R';

                content += `<div class="card-header">
                                        <a class="btn" data-bs-toggle="collapse" href="#collapse${node.id}">
                                            <img class="tree_image w-h-100-p" src="{{ asset('assets/images/default-member.png')}}" style = "width : ${imageSize[depth]}px; height : ${imageSize[depth]}px;" alt="">
                                                <span style = "margin-left : 20px; font-size: ${fontSize[depth]}px">
                                                <h5 style = "color : white; background-color : black; border-radius: 5px">Level ${depth} (${pos})</h5>
                                                ${node.username}
                                                </span>
                                        </a>
                                        <span style = "float : right; padding: 10px; font-size : ${memberFontSize[depth]}px"> members : ${node.member} </span>
                                        <div id="collapse${node.id}" class="collapse" data-bs-parent="#accordion">`;
                    var posIdThree = treeData[plan_num].tree.filter(item => item.pos_id === node.id);
                    posIdThree.map(newNode => {
                        content = recursive_tree(newNode, plan_num, depth + 1, content);
                    });

                content += `</div></div>`; // Close the inner div

                return content;
            }

            function generate_each_tree(){
                for(i = 1; i <= objectLengh; i++){
                    htmlContent[i] = ``;
                    htmlContent[i] += recursive_tree(treeData[i].tree[0], i, 0, htmlContent[i]);
                    
                    htmlContent[i] += `</div>`;
                    // console.log(htmlContent[i] + '\n');
                }
            }

            
            // ++++++++++ ++++++++++++++++++++ tree data end +++++++++++++++++++++++++++++++++

            var trees = [{ 'plan_id': '1', 'plan_price': '12', 'plan_name': 'Silver', 'plan_system_fee': 10, 'plan_distribution': '3.6', 'plan_size': '3', tree: { 'user_id': '1', 'username': 'harry01' } },
            { 'plan_id': '2', 'plan_price': '36', 'plan_name': 'Gold', 'plan_system_fee': 10, 'plan_distribution': '8.1', 'plan_size': '4', tree: { 'user_id': '2', 'username': 'harry02' } },
            ];

            

            // for (i = 0; i < objectLengh; i++) {
            //     console.log(trees[i].plan_id, "    ", trees[i].plan_price, "           ", trees[i].tree.user_id, "         ", trees[i].tree.username);
            //     console.log('\n');
            // }
            function showPanel() {
                console.log('showing panels');
                $('#treePanel').show();
                $('#treeAccordian').hide();
            }

            var panelContent = ``;
            for (i = 1; i <= objectLengh; i++) {
                panelContent = panelContent + `
                                <div class = "col-xl-4 col-md-6 mb-4">
                                    <div class = "card custom--card dashboard-plan-card">
                                        <div class="card-body">
                                            <div class="pricing-table mb-4 text-center">
                                                <h4 class="package-name mb-2 text-center">
                                                <strong>${treeData[i].plan_name}</strong>
                                            </h4>
                                            <span class="price text--dark fw-bold d-block">
                                                $${parseInt(treeData[i].plan_price, 10)}
                                            </span>
                                            <hr>
                                            <ul class="package-features-list mt-3">
                                                <li>
                                                    <i class="fas fa-check bg--success"></i>
                                                    <span>System Fee : ${treeData[i].plan_fee}%</span>
                                                    <span class="icon" data-bs-toggle="modal" data-bs-target="#bvInfoModal">
                                                        <i class="fas fa-question-circle"></i>
                                                    </span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-check bg--success"></i>
                                                    <span>@lang('Distribution'): $${treeData[i].plan_distribution}</span>
                                                    <span class="icon" data-bs-toggle="modal" data-bs-target="#refComInfoModal">
                                                        <i class="fas fa-question-circle"></i>
                                                    </span>
                                                </li>
                                                <li>
                                                    <i
                                                        class="fas fa-check bg--success"></i>
                                                    <span>Plan Size: 
                                                        3 * ${treeData[i].plan_size}</span>
                                                    <span class="icon" data-bs-toggle="modal" data-bs-target="#treeComInfoModal">
                                                        <i class="fas fa-question-circle"></i>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="btn btn--base subscribeBtn w-100 mt-2" onclick = "showTree(${i})" type="button">
                                                View in details
                                            </button>
                                    </div>
                                </div>
                            </div>
                            `
            }

            $('#treePanel').html(panelContent);

            var treeContent = [];

            function initTreeContent() {
                for (i = 1; i <= objectLengh; i++) {
                    treeContent[i] = ``;
                }
                for (i = 1; i <= objectLengh; i++) {
                    treeContent[i] = treeContent[i] + `
                        <div class = "row">
                            <button class = "btn btn-primary form-control" onclick = "showPanel()">
                                <h3 style = "color : white">Back</h3>
                            </div>
                        </div>
                    `;
                }
                generate_each_tree();
            }

            function showTree(index) {
                console.log(index + '\n');
                $('#treePanel').hide();
                initTreeContent();
                treeContent[index] = treeContent[index] + htmlContent[index];
                $('#treeAccordian').html(treeContent[index]);
                $('#treeAccordian').show();
            }

            for (i = 0; i < toggler.length; i++) {
                toggler[i].addEventListener("click", function () {
                    this.parentElement.querySelector(".nested").classList.toggle("active");
                    this.classList.toggle("caret-down");
                });
            }




            (function ($) {
                $('.showDetails').on('click', function () {
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
                    $('.cbv').text($(this).data('cbv'));
                    $('.lpaid').text($(this).data('lpaid'));
                    $('.cpaid').text($(this).data('cpaid'));
                    $('.rpaid').text($(this).data('rpaid'));
                    $('.lfree').text($(this).data('lfree'));
                    $('.cfree').text($(this).data('cfree'));
                    $('.rfree').text($(this).data('rfree'));
                    $('#exampleModalCenter').modal('show');
                });

                $('#copyBoard').click(function () {
                    var copyText = document.getElementsByClassName("copyURL");
                    copyText = copyText[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);

                    /*For mobile devices*/
                    document.execCommand("copy");
                    $('.copyText').text('Copied');
                    setTimeout(() => {
                        $('.copyText').text('Copy');
                    }, 2000);
                });
                $('#copyBoard2').click(function () {
                    var copyText = document.getElementsByClassName("copyURL2");
                    copyText = copyText[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);

                    /*For mobile devices*/
                    document.execCommand("copy");
                    $('.copyText2').text('Copied');
                    setTimeout(() => {
                        $('.copyText2').text('Copy');
                    }, 2000);
                });

                $('#copyBoard3').click(function () {
                    var copyText = document.getElementsByClassName("copyURL3");
                    copyText = copyText[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);

                    /*For mobile devices*/
                    document.execCommand("copy");
                    $('.copyText3').text('Copied');
                    setTimeout(() => {
                        $('.copyText3').text('Copy');
                    }, 2000);
                });




            })(jQuery);
        </script>
    @endpush