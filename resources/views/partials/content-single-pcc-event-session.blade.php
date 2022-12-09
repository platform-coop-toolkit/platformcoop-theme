<div class="entry-content" id="content">
  @if ($user_can_access)
    @content
    @if(!empty(SinglePccEvent::eventParticipants()))
    <div class="wp-block-columns has-2-columns">
      <div class="wp-block-column">
        @if($parent_event_type === 'course' || $parent_event_type === 'past_course')
          @if ($event_instructor_type === 'coach')
            <h2>{{ __('Coaches', 'pcc')}}</h2>
          @else
            <h2>{{ __('Instructors', 'pcc')}}</h2>
          @endif
        @else
          <h2>{{ __('Speakers', 'pcc')}}</h2>
        @endif
      </div>
      <div class="wp-block-column">
        <ul class="participants cards cards--two-columns">
        @foreach(SinglePccEvent::eventParticipants() as $participant)
          @include('partials/event-participant')
        @endforeach
        </ul>
      </div>
    </div>
    @endif
  @else
    <p>{{ __('You do not have permission to access this content.', 'pcc') }}</p>
  @endif
  <p class="wp-block-button is-style-secondary">
    @if($parent_event_type === 'course' || $parent_event_type === 'past_course')
      <a class="wp-block-button__link" href="{{ get_permalink($post->post_parent) }}">{{ __('Back to course', 'pcc') }}</a>
    @else
      <a class="wp-block-button__link" href="{{ get_permalink($post->post_parent) }}program/">{{ __('Back to program', 'pcc') }}</a>
    @endif
  </p>
</div>