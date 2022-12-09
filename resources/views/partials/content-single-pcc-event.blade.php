@php
$user_id = get_current_user_id();
$user_is_external_user = user_has_role($user_id, 'external_user');
$user_has_event = user_has_event($user_id, get_the_ID());
@endphp

<div class="entry-content" id="content">
  @if(!(get_post_meta(get_the_ID(), 'pcc_event_oc_paid_event', true) && (!is_user_logged_in() || (is_user_logged_in() && $user_is_external_user && !$user_has_event))))
    @content
  @endif 
  @if($oc_content = get_post_meta(get_the_ID(), 'pcc_event_oc_content_before_link', true))
    {!! $oc_content !!}
  @endif
  @if($oc_event_url = get_post_meta(get_the_ID(), 'pcc_event_oc_event_link', true))
    @php
    $oc_event_embed_url = get_post_meta(get_the_ID(), 'pcc_event_oc_event_embed_link', true);
    @endphp
    @if($oc_event_embed_url && !empty($oc_event_embed_url))
      <p><iframe src="{{$oc_event_embed_url}}" style="width: 100%; height: 130vh; border: none;"></iframe></p>
    @else
      <p><a class="payment" href="{{$oc_event_url}}" target="_blank">{{ __('Payment', 'pcc') }}</a></p>
    @endif
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
