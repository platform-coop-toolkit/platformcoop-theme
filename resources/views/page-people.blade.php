{{--
  Template Name: People Page
--}}

@extends('layouts.app')

@section('content')
  @include('partials.page-header')
    <div id="content">
      @foreach([
        [
          'title' => __('Research Fellows', 'pcc'),
          'query' => $research_fellows_query,
        ],
        [
          'title' => __('Affiliate Researchers', 'pcc'),
          'query' => $research_affiliate_institute_query,
        ],
        [
          'title' => __('Affiliate Faculty', 'pcc'),
          'query' => $affiliate_faculty_query,
        ],
        [
          'title' => __('Staff', 'pcc'),
          'query' => $staff_query,
        ],
        [
          'title' => __('Council of Advisors', 'pcc'),
          'query' => $council_query,
        ],
        [
          'title' => __('Student Fellows', 'pcc'),
          'query' => $student_fellows_query,
        ]
        ] as $query)
        @if ($query['query']->have_posts())
        <div class="wp-block-group">
          <h2 id="{{ sanitize_title($query['title']) }}">{{ $query['title'] }}</h2>
          <div class="cards cards--three-columns">
          @while ($query['query']->have_posts()) @php $query['query']->the_post() @endphp
            @include('partials.content-pcc-person')
          @endwhile
          @php(wp_reset_postdata())
          </div>
        </div>
        @endif
      @endforeach
  </div>
@endsection
