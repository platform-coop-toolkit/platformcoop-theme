<li class="session">
    <time class="session__time" datetime="{{ strftime('%FT%T', $session->pcc_event_start) }}">{{ strftime('%l:%M%p', $session->pcc_event_start) }}</time>
    <div class="session__info">
      <p class="session__title">
        <a href="@permalink($session->ID)">@title($session->ID)&nbsp;@svg('chevron-right', ['aria-hidden' => 'true', 'viewBox' => '0 0 5.886 9.8'])</a>
      </p>
      @if(SinglePccEvent::sessionVenue($session->ID, $session->post_parent))
      <p class="session__location">@svg('location', ['aria-hidden' => true, 'viewBox' => '0 0 10.581 15.183']) {!! SinglePccEvent::sessionVenue($session->ID, $session->post_parent) !!}</p>
      @endif
      @if(SinglePccEvent::sessionParticipants($session->ID))
      <p class="session__participants">{!! implode(', ', SinglePccEvent::sessionParticipants($session->ID)) !!}</p>
      @endif
    </div>
</li>
