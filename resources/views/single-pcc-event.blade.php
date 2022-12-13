@extends('layouts.app')

@section('content')
  <div @php post_class('event') @endphp>
    @if(has_post_thumbnail())
    <div class="event-banner @if($banner_video){{ 'event-banner--video'}}@endif">
      <picture class="event-banner__picture">
        <source srcset="{{ get_the_post_thumbnail_url($post, 'event-banner') }}" media="(min-width: 600px)">
        {!! get_the_post_thumbnail($post, 'event-banner-mobile') !!}
      </picture>
      @if($banner_video)
      <video class="event-banner__video" autoplay loop>
        <source src="{{ $banner_video }}" type="video/mp4">
        {{__('Sorry, your browser doesn\'t support embedded videos.', 'pcc') }}
      </video>
      @endif
    </div>
    @endif
    @if($post->post_parent || $event_type === 'conference' || $event_type === 'course' || $event_type === 'past_course')
      @include('partials.event-ribbon')
    @endif
    <header class="event-header">
      <section>
        @include('partials/breadcrumb')
        @if(!$post->post_parent)
        <p class="event-type">{{ $event_type_label }}</p>
        @endif
        <h1 class="entry-title">{!! App::title() !!}</h1>
      </section>
      @include('partials/event-meta')
    </header>
      @while(have_posts()) @php the_post() @endphp
        @if($event_view)
          @include('partials.content-single-pcc-event-'.$event_view)
        @else
          @include('partials.content-single-pcc-event')
        @endif
      @endwhile
  </div>
@endsection
