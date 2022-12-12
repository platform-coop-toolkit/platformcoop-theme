<div class="meta">
  <ul>
    <li><span>@svg('calendar', ['aria-hidden' => 'true'])</span><p>{{ $event_date }}</p></li>
    @if($event_venue)
      @if($post->post_parent)
        <li><span>@svg('location', ['aria-hidden' => 'true'])</span><p translate="no">{!! SinglePccEvent::sessionVenue($post->ID) !!}</p></li>
      @else
        <li><span>@svg('location', ['aria-hidden' => 'true'])</span><p translate="no">{!! trim(strip_tags($event_venue, ['br'])) !!}</p></li>
      @endif
    @endif
    <!-- <li><span>@svg('currency', ['aria-hidden' => 'true'])</span><p translate="no">40 - 100 USD</p></li>
    <li><span>@svg('certificate', ['aria-hidden' => 'true'])</span><p translate="no">Certificate Course</p></li>
    <li><span>@svg('web', ['aria-hidden' => 'true'])</span><p translate="no">English + Spanish (Live Transl.)</p></li> -->
  </ul>
</div>