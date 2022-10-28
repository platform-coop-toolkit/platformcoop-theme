@php
$user_id = get_current_user_id();
$user_is_external_user = user_has_role($user_id, 'external_user');
$user_has_event = user_has_event($user_id, get_the_ID());
@endphp

<div class="entry-content" id="content">
  @if(get_post_meta(get_the_ID(), 'pcc_event_oc_paid', true) && (!is_user_logged_in() || (is_user_logged_in() && $user_is_external_user && !$user_has_event)))
    @if($oc_content = get_post_meta(get_the_ID(), 'pcc_event_oc_content_before_link', true))
      {!! $oc_content !!}
    @endif
    @if($oc_collective_slug = get_post_meta(get_the_ID(), 'pcc_event_oc_collective_slug', true))
      @php
      $oc_button_type = get_post_meta(get_the_ID(), 'pcc_event_oc_button_type', true);
      $oc_button_color = get_post_meta(get_the_ID(), 'pcc_event_oc_button_color', true);
      @endphp
      <script src="https://opencollective.com/{{$oc_collective_slug}}/{{$oc_button_type}}/button.js" color="{{$oc_button_color}}"></script>
    @endif
  @else
    @content
  @endif
  @if(!empty(SinglePccEvent::eventParticipants(6)))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      <h2>{{ __('Participants', 'pcc')}}</h2>
    </div>
    <div class="wp-block-column">
      <ul class="participants cards cards--two-columns">
      @foreach(SinglePccEvent::eventParticipants(6, true) as $participant)
        @include('partials/event-participant')
      @endforeach
      </ul>
      <p class="wp-block-button is-style-secondary">
        <a class="wp-block-button__link" href="@permalink()participants/">{{ __('See all participants', 'pcc') }}</a>
      </p>
    </div>
  </div>
  @endif
  @if(!empty($event_sponsors))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      <h2>{{ __('Sponsors', 'pcc')}}</h2>
    </div>
    <div class="wp-block-column">
      <ul class="sponsors">
        @foreach($event_sponsors as $sponsor)
          @include('partials/event-sponsor')
        @endforeach
      </ul>
    </div>
  </div>
  @endif
</div>
