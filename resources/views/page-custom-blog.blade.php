{{--
  Template Name: Blog
--}}

@extends('layouts.app')

@section('content')
  @include('partials.page-header')
    <div id="content">
      <div class="wp-block-group">
        <div class="cards cards--three-columns">
        @if (!have_posts())
          <div class="alert alert-warning">
            {{ __('Sorry, no results were found.', 'pcc') }}
          </div>
          {!! get_search_form(false) !!}
        @endif

        @php
            global $paged;
            $curpage = $paged ? $paged : 1;
            $args = [
              'post_type' => 'post',
              'showposts' => 12,
              'orderby' => 'date',
              'order'   => 'DESC',
              // 'cat' => 3,
              'paged' => $paged
            ];
            $blog_posts = new WP_Query($args);
        @endphp
        @while ($blog_posts->have_posts()) @php $blog_posts->the_post() @endphp
          @include('partials.content-'.get_post_type())
        @endwhile
      </div>

      <nav class="navigation pagination" aria-label="Posts">
        {!!
          paginate_links([
            'base' => get_pagenum_link(1) . '%_%',
            'format' => 'page/%#%',
            'current' => max( 1, get_query_var( 'paged' ) ),
            'total' => $blog_posts->max_num_pages,
            'mid_size'    => 1,
            'prev_text'    => __('‹ '),
            'next_text'    => __(' ›'),
          ]);
          wp_reset_postdata();
        !!}
      </nav>
    </div>
  </div>
@endsection
