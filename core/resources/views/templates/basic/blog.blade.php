@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section padding-top padding-bottom">
        <div class="container">
            <div class="row mb-30-none">
                @foreach ($blogs as $blog)
                    <div class="col-md-6 col-xl-4 col-sm-10">
                        <div class="post-item">
                            <div class="post-thumb c-thumb">
                                <a href="{{ route('blog.details', [$blog->id, slug($blog->data_values->title)]) }}">
                                    <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->blog_image, '410x410') }}" alt="blog">
                                </a>
                            </div>
                            <div class="post-content">
                                <h5 class="title">
                                    <a href="{{ route('blog.details', [$blog->id, slug($blog->data_values->title)]) }}">
                                        {{ __(@$blog->data_values->title) }}
                                    </a>
                                </h5>
                                <ul class="meta-post">
                                    <li>
                                        <i class="fas fa-calendar-day"></i>
                                        <span>{{ showDateTime($blog->created_at, $format = 'd M, Y') }}</span>
                                    </li>
                                </ul>
                                <div class="entry-content">
                                    <p>{{ __(strLimit(strip_tags(@$blog->data_values->description), 165)) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($blogs->hasPages())
                {{ paginateLinks($blogs) }}
            @endif
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
