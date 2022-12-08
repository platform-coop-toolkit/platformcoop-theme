<div class="entry-content" id="content">
  @content
  @if(!empty(SinglePccEvent::eventParticipants()))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      <h2>{{ __('Speakers', 'pcc')}}</h2>
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
  <p class="wp-block-button is-style-secondary">
    <a class="wp-block-button__link" href="{{ get_permalink($post->post_parent) }}program/">{{ __('Back to program', 'pcc') }}</a>
  </p>
</div>