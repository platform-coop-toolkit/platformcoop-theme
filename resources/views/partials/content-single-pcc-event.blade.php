<div class="entry-content" id="content">
  @content
  @include('partials/share-buttons')
  @if(!empty(SinglePccEvent::eventParticipants(6)))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      @if($event_type === 'course' || $event_type === 'past_course')
        @if ($event_instructor_type === 'coach')
          <h2>{{ __('Coaches', 'pcc')}}</h2>
        @else
          <h2>{{ __('Instructors', 'pcc')}}</h2>
        @endif
      @else
        <h2>{{ __('Participants', 'pcc')}}</h2>
      @endif
    </div>
    <div class="wp-block-column">
      <ul class="participants cards cards--two-columns">
      @foreach(SinglePccEvent::eventParticipants(6, true) as $participant)
        @include('partials/event-participant')
      @endforeach
      </ul>
      <p class="wp-block-button is-style-secondary">
        @if($event_type === 'course' || $event_type === 'past_course')
          @if ($event_instructor_type === 'coach')
            <a class="wp-block-button__link" href="@permalink()coaches/">{{ __('See all coaches', 'pcc') }}</a>
          @else
            <a class="wp-block-button__link" href="@permalink()instructors/">{{ __('See all instructors', 'pcc') }}</a>
          @endif
        @else
        <a class="wp-block-button__link" href="@permalink()participants/">{{ __('See all participants', 'pcc') }}</a>
        @endif
      </p>
    </div>
  </div>
  @endif
  @if(!empty($event_sponsors))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      @if($event_type === 'course' || $event_type === 'past_course')
        <h2>{{ __('Partners', 'pcc')}}</h2>
      @else
        <h2>{{ __('Sponsors', 'pcc')}}</h2>
      @endif
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
  @if(!empty($event_classes))
  <div class="wp-block-columns has-2-columns">
    <div class="wp-block-column">
      <h2>{{ __('Learning', 'pcc')}}<br/>{{ __('Journey', 'pcc')}}</h2>
    </div>
    <div class="wp-block-column">
      <ul class="classes">
        @foreach($event_classes as $class)
          <div class="class-heading">
            <h3>{{ $class['title'] }}</h3>
            <span>
              <p>{{ sprintf(__('with %s', 'pcc'), get_the_title($class['instructor'])) }}</p>
              @php
                $date = strtotime($class['date']);
                $start_time = strtotime($class['start_time']);
                $end_time = strtotime($class['end_time']);
              @endphp
              <p>{{ date('F j, Y',$date) }} - {{ date('gA',$start_time) }}-{{ date('gA',$end_time) }} {{ $event_timezone }}</p>
            </span>
          </div>
          <div class="class-topics">
            <ul>
              @foreach($class['topics'] as $topic)
                <li>{{ $topic }}</li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </ul>
    </div>
  </div>
  @endif
  @if($oc_event_url = get_post_meta(get_the_ID(), 'pcc_event_oc_event_link', true))
    <div class="wp-block-no-columns">
      <div class="wp-block-column">
        <h2>{{ __('Payment', 'pcc')}}</h2>
      </div>
      <div class="wp-block-column">
        @php
        $oc_event_embed_url = get_post_meta(get_the_ID(), 'pcc_event_oc_event_embed_link', true);
        @endphp
        @if($oc_event_embed_url && !empty($oc_event_embed_url))
          <p><iframe src="{{$oc_event_embed_url}}" style="width: 100%; height: 130vh; border: none;"></iframe></p>
        @else
          <p><a class="payment" href="{{$oc_event_url}}" target="_blank">{{ __('Payment', 'pcc') }}</a></p>
        @endif
      </div>
    </div>
  @endif
</div>
