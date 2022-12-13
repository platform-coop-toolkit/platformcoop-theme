<div class="entry-content" id="content">
  <div class="wp-block-group">
    @if ($event_type === 'course' || $event_type === 'past_course')
      @if ($event_instructor_type === 'coach')
        <p><em>{{ sprintf(__('Coaches are shown in %s order.', 'pcc'), $participant_order) }}</em></p>
      @else
        <p><em>{{ sprintf(__('Instructors are shown in %s order.', 'pcc'), $participant_order) }}</em></p>
      @endif
    @else
      <p><em>{{ sprintf(__('Participants are shown in %s order.', 'pcc'), $participant_order) }}</em></p>
    @endif
    <p class="wp-block-button is-style-secondary">
      <a class="wp-block-button__link" href="{{ $reorder_participants['link'] }}">{{ $reorder_participants['label'] }}</a>
    </p>
  </div>
  @if(!empty(SinglePccEvent::eventParticipants()))
  <div class="wp-block-group">
    <ul class="participants cards cards--three-columns">
    @foreach(SinglePccEvent::eventParticipants() as $participant)
      @include('partials/event-participant')
    @endforeach
    </ul>
  </div>
  @else
    <div class="wp-block-group">
    @if ($event_type === 'course' || $event_type === 'past_course')
     
      @if ($event_instructor_type === 'coach')
        <p>{{ __('No coaches were found.', 'pcc') }}</p>
      @else
        <p>{{ __('No instructors were found.', 'pcc') }}</p>
      @endif
    @else
      <p>{{ __('No participants were found.', 'pcc') }}</p>
    @endif
    </div>
  @endif
</div>
