@php
  $post = get_post();
  $event_type = get_post_meta($post->ID, 'pcc_event_type', true);
  $event_price = get_post_meta($post->ID, 'pcc_event_price', true);

  $start = (int) get_post_meta($post->ID, 'pcc_event_start', true);
  $end = (int) get_post_meta($post->ID, 'pcc_event_end', true);
  $event_date;

  $multiday = strftime('%F', $start) !== strftime('%F', $end);

  if ($multiday) {
      $same_month = strftime('%m', $start) === strftime('%m', $end);
      if ($same_month) {
        $event_date = strftime('%B %e', $start) . '–' . ltrim(strftime('%e, %Y', $end));
      } else {
        $event_date = strftime('%B %e', $start) . '–' . ltrim(strftime('%B %e, %Y', $end));
      }
  } else {
      $same_meridian = strftime('%P', $start) === strftime('%P', $end);
      if ($same_meridian) {
        $event_date = strftime('%h %e, %Y %l:%M', $start) . '–' . ltrim(strftime('%l:%M%p', $end));
      } else {
        $event_date = strftime('%h %e, %Y %l:%M%p', $start) . '–' . ltrim(strftime('%l:%M%p', $end));
      }
  }
@endphp

@if($post->post_type == 'pcc-event' && ($event_type == 'course' || $event_type == 'past_course'))
    <div class="course-price">{!! $event_price ? $event_price . ' USD' : 'Free' !!}</div>
    <time class="updated" datetime="@published('c')">{{ $event_date !== '–' ? $event_date : ''}}</time>
@else
<time class="updated" datetime="@published('c')">@published('M j, Y')</time>
@endif