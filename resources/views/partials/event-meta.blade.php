<div class="meta">
  <ul>
    <li><span>@svg('calendar', ['aria-hidden' => 'true'])</span><p>{{ $event_date }}</p></li>
    @if($event_format)
      @if($post->post_parent)
        <li><span>@svg('location', ['aria-hidden' => 'true'])</span><p translate="no">{!! SinglePccEvent::sessionVenue($post->ID, $post->post_parent) !!}</p></li>
      @else
        <li><span>@svg('location', ['aria-hidden' => 'true'])</span><p translate="no">{!! trim(strip_tags($event_format, ['br'])) !!}</p></li>
      @endif
    @endif
    @if($post->post_type == 'pcc-event' && ($event_type == 'course' || $event_type == 'past_course'))
      <li><span>@svg('currency', ['aria-hidden' => 'true'])</span><p translate="no">{!! $event_price !!}</p></li>
    @endif
    @if($event_certificate)
      <li><span>@svg('certificate', ['aria-hidden' => 'true'])</span><p translate="no">{{ __('Certificate Course', 'pcc') }}</p></li>
    @endif
    @if($event_language)
      <li><span>@svg('web', ['aria-hidden' => 'true'])</span><p translate="no">{{ $event_language }}</p></li>
    @endif
  </ul>
</div>
