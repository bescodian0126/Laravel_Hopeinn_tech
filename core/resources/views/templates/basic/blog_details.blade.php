@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section padding-bottom padding-top">
        <div class="container">
            <div class="row justify-content-center justify-content-lg-between">
                <div class="col-lg-7 col-xl-8 mb-md-3 mb-lg-0 mb-5">
                    <div class="post-item post-details">
                        <div class="post-thumb c-thumb">
                            <img src="{{ getImage('assets/images/frontend/blog/' . $blog->data_values->blog_image, '770x450') }}" alt="blog">
                        </div>
                        <div class="post-content">
                            <h5 class="title">{{ __($blog->data_values->title) }}</h5>
                            <ul class="meta-post justify-content-start">

                                <li>
                                    <i
                                        class="fas fa-calendar-day"></i><span>{{ showDateTime($blog->created_at, $format = 'd F, Y') }}</span>
                                </li>
                            </ul>
                            <div class="entry-content">
                                <p>@php echo $blog->data_values->description; @endphp</p>

                                <div class="tag-options">
                                    <div class="share">
                                        <span>@lang('Share now:')</span>
                                        <a class="text-dark p-2" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        <a class="text-dark p-2" href="https://twitter.com/intent/tweet?text={{ __($blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                        <a class="text-dark p-2" href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                        <a class="text-dark p-2" href="https://www.instagram.com/?url={{ urlencode(url()->current()) }}"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="comments-area">
                        <div class="fb-comments" data-href="{{ route('blog.details', [$blog->id, slug($blog->data_values->title)]) }}" data-numposts="5"></div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-5 col-xl-4">
                    <aside class="blog-sidebar">
                        <div class="widget widget-post">
                            <h6 class="title"><i class="flaticon-scroll"></i>@lang('Latest Blog')</h6>
                            <ul>
                                @foreach ($latestBlogs as $latestBlog)
                                    <li>
                                        <a
                                            href="{{ route('blog.details', [$latestBlog->id, slug($latestBlog->data_values->title)]) }}">
                                            <div class="thumb">
                                                <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$latestBlog->data_values->blog_image, '410x410') }}" alt="blog">

                                            </div>
                                            <div class="content">
                                                <h6 class="subtitle">
                                                    {{ __(strLimit(@$latestBlog->data_values->title, 60)) }}</h6>
                                                <span>{{ showDateTime(@$latestBlog->created_at, $format = 'd F, Y') }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
