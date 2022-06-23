{{--
  Template Name: Blog Category View
--}}

@php
  global $paged;
  $curpage = $paged ? $paged : 1;
  $category = carbon_get_the_post_meta('category') ?? -1;

  $args_posts = [
    'post_type' => 'post',
    'showposts' => 12,
    'orderby' => 'date',
    'order'   => 'DESC',
    'cat' => $category,
    'paged' => $paged
  ];
  $blog_posts = new WP_Query($args_posts);

  $args_paginate = [
    'base' => get_pagenum_link(1) . '%_%',
    'format' => 'page/%#%',
    'current' => max( 1, get_query_var( 'paged' ) ),
    'total' => $blog_posts->max_num_pages,
    'mid_size'    => 1,
    'prev_text'    => __('‹ '),
    'next_text'    => __(' ›'),
  ];
  $paginate_links = paginate_links($args_paginate);
@endphp

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
        @while ($blog_posts->have_posts()) @php $blog_posts->the_post() @endphp
          @include('partials.content-'.get_post_type())
        @endwhile
      </div>

      <nav class="navigation pagination" aria-label="Posts">
        @php
          echo $paginate_links;
          wp_reset_postdata();
        @endphp
      </nav>
    </div>
  </div>
@endsection
