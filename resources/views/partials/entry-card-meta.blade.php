@php
  $post = get_post();
  $event_type = get_post_meta($post->ID, 'pcc_event_type')[0];
  $event_price = get_post_meta($post->ID, 'pcc_event_price')[0];
@endphp
@if($post->post_type == 'pcc-event' && ($event_type == 'course' || $event_type == 'past_course'))
    <div class="course-price">{!! $event_price ? $event_price . ' USD' : 'Free' !!}</div>
@endif
<time class="updated" datetime="@published('c')">@published('M j, Y')</time>
