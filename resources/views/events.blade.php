{{--
  Template Name: Events
--}}

@php
  global $paged;
  $curpage = $paged ? $paged : 1;
  $event_type = carbon_get_the_post_meta('crb_event_type') ?? -1;
  $exclude_paid_event_children = [];

  if ($event_type != 'all') {
    $meta_query = array(
      array(
          'key' => 'pcc_event_type',
          'value' => $event_type,
          'compare' => '='
      )
    );
  }

  $child_posts = get_posts([
    'post_type' => 'pcc-event',
    'posts_per_page' => -1,
    'post_parent__not_in' => [0],
    'fields' => 'ids',
  ]);

  foreach ($child_posts as $child_id) {
    $event_parents = get_post_ancestors($child_id);
    $parent_id = array_pop($event_parents);
    $is_paid_event = get_post_meta($parent_id, 'pcc_event_price', true);

    if (!empty($is_paid_event)) {
      $exclude_paid_event_children[] = $child_id;
    }
  }

  $args_posts = [
    'post_type' => 'pcc-event',
    'posts_per_page' => 12,
    'orderby' => 'date',
    'order'   => 'DESC',
    'paged' => get_query_var('paged', 1),
    'meta_query' => $meta_query,
    'post__not_in' => $exclude_paid_event_children,
  ];

  $events = new WP_Query($args_posts);

  $args_paginate = [
    'base' => get_pagenum_link(1) . '%_%',
    'format' => '/page/%#%',
    'current' => $curpage,
    'total' => $events->max_num_pages,
    'mid_size'    => 1,
    'prev_text'    => __('‹ '),
    'next_text'    => __(' ›'),
  ];

  $paginate_links = paginate_links($args_paginate);
  
@endphp

@extends('layouts.app')

@section('content')
  @include('partials.page-header')
  <div id="pcc-event-template-unique-id" class="content">
    <div class="wp-block-group">
        <div class="cards cards--three-columns events-container">
        @if (!have_posts())
          <div class="alert alert-warning">
            {{ __('Sorry, no results were found.', 'pcc') }}
          </div>
          {!! get_search_form(false) !!}
        @endif
        @while ($events->have_posts()) @php $events->the_post() @endphp
          @include('partials.content-' . get_post_type())
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
