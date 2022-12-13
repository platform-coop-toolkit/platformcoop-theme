<?php

namespace App\Controllers;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Sober\Controller\Controller;

class SinglePccEvent extends Controller
{
    public static function eventParticipants($limit = -1, $featured = false)
    {
        global $id, $wp;
        $output = [];

        if ($featured) {
            $featured_output = [];
            $featured_participants = get_post_meta($id, 'pcc_event_featured_participants', true);
            if ($featured_participants) {
                foreach ($featured_participants as $participant_id) {
                    $name = get_the_title($participant_id);
                    $featured_output[ $name ] = [
                        'name' => $name,
                        'short_title' => get_post_meta($participant_id, 'pcc_person_short_title', true),
                        'headshot' => get_post_thumbnail_id($participant_id),
                        'slug' => get_post_field('post_name', $participant_id),
                    ];
                }
            }
        }

        $participants = get_post_meta($id, 'pcc_event_participants', true);
        if ($participants) {
            foreach ($participants as $participant_id) {
                $name = get_the_title($participant_id);
                $output[ $name ] = [
                    'name' => $name,
                    'short_title' => get_post_meta($participant_id, 'pcc_person_short_title', true),
                    'headshot' => get_post_thumbnail_id($participant_id),
                    'slug' => get_post_field('post_name', $participant_id),
                ];
            }
        }
        if (isset($wp->query_vars['participants'])) {
            switch ($wp->query_vars['participants']) {
                case 'alphabetical':
                    ksort($output);
                    break;
                case 'random':
                default:
                    shuffle($output);
                    break;
            }
        } else {
            shuffle($output);
        }

        if ($featured) {
            $output = $featured_output + $output;
        }

        if ($limit !== -1 && $limit >= 1) {
            return array_slice($output, 0, $limit);
        }
        return $output;
    }

    public static function sessionParticipants($id)
    {
        $output = [];
        $participants = get_post_meta($id, 'pcc_event_participants', true);
        if ($participants) {
            foreach ($participants as $participant_id) {
                $name = get_the_title($participant_id);
                $output[] = strip_tags($name);
            }
        }
        return $output;
    }

    public static function sessionVenue($id, $parent_id)
    {
        $formats = [
            'async' => __('Online Asynchronous', 'pcc'),
            'sync' => __('Online Synchronous', 'pcc'),
            'async_sync' => __('Online Asynchronous and Synchronous', 'pcc'),
        ];

        $parent_event_format = get_post_meta($parent_id, 'pcc_event_format', true);

        if ($parent_event_format === 'not_online' || $parent_event_format == null) {
            $venue_name = get_post_meta($id, 'pcc_event_venue', true);
            $venue_street_address = get_post_meta($id, 'pcc_event_venue_street_address', true);
            if ($venue_name && $venue_street_address) {
                return implode('<br />', [nl2br($venue_name), $venue_street_address]);
            } elseif ($venue_name) {
                return nl2br($venue_name);
            }
            return false;
            
        }

        return $formats[ $parent_event_format ];
    }



    public function reorderParticipants()
    {
        global $post, $wp;
        if (isset($wp->query_vars['participants'])) {
            switch ($wp->query_vars['participants']) {
                case 'alphabetical':
                    $output = [
                        'link' => get_permalink($post) . 'participants/',
                        'label' => __('View in Random Order', 'pcc')
                    ];
                    break;
                case 'random':
                    $output = [
                        'link' => get_permalink($post) . 'participants/alphabetical/',
                        'label' => __('View in Alphabetical Order', 'pcc')
                    ];
                    break;
            }
        } else {
            $output = [
                'link' => get_permalink($post) . 'participants/alphabetical/',
                'label' => __('View in Alphabetical Order', 'pcc')
            ];
        }
        return $output;
    }

    public function participantOrder()
    {
        global $wp;
        if (isset($wp->query_vars['participants']) &&
            in_array($wp->query_vars['participants'], ['alphabetical', 'random'], true)
        ) {
            return $wp->query_vars['participants'];
        }
        return false;
    }

    public function bannerVideo()
    {
        global $post;
        $banner_video = get_post_meta($post->ID, 'pcc_event_banner_video', true);
        if ($banner_video) {
            return $banner_video;
        }
        return false;
    }

    public function eventView()
    {
        global $post, $wp;

        if (isset($wp->query_vars['participants'])) {
            return (in_array($wp->query_vars['participants'], ['alphabetical', 'random'], true)) ?
                'participants' :
                'participant';
        }

        if (isset($wp->query_vars['program'])) {
            return ($wp->query_vars['program'] === 'yes') ? 'program' : false;
        }

        if ($post->post_parent) {
            return 'session';
        }

        return false;
    }

    public function eventParticipant()
    {
        global $wp;

        if (isset($wp->query_vars['participants'])) {
            if (!in_array($wp->query_vars['participants'], ['alphabetical', 'random'], true)) {
                $participant = get_page_by_path(
                    $wp->query_vars['participants'],
                    'OBJECT',
                    'pcc-person'
                );
                if (!$participant) {
                    return false;
                } else {
                    return $participant;
                }
            }
        }

        return false;
    }

    public function eventDate()
    {
        global $id;
        $start = (int) get_post_meta($id, 'pcc_event_start', true);
        $end = (int) get_post_meta($id, 'pcc_event_end', true);
        $multiday = strftime('%F', $start) !== strftime('%F', $end);
        if ($multiday) {
            $same_month = strftime('%m', $start) === strftime('%m', $end);
            if ($same_month) {
                $output = strftime('%B %e', $start) . '–' . ltrim(strftime('%e, %Y', $end));
            } else {
                $output = strftime('%B %e', $start) . '–' . ltrim(strftime('%B %e, %Y', $end));
            }
        } else {
            $same_meridian = strftime('%P', $start) === strftime('%P', $end);
            if ($same_meridian) {
                $output = strftime('%h %e, %Y %l:%M', $start) . '–' . ltrim(strftime('%l:%M%p', $end));
            } else {
                $output = strftime('%h %e, %Y %l:%M%p', $start) . '–' . ltrim(strftime('%l:%M%p', $end));
            }
        }
        return $output;
    }

    public function eventType()
    {
        global $id;
        return get_post_meta($id, 'pcc_event_type', true);
    }

    public function parentEventType()
    {
        global $post;
        return get_post_meta($post->post_parent, 'pcc_event_type', true);
    }

    public function eventLanguage()
    {
        global $id;
        $event_type = $this->eventType();

        $languages = [
            'en'=> 'English',
            'pt'=> 'Portuguese',
            'es'=> 'Spanish',
            'it'=> 'Italian',
            'tr'=> 'Turkish',
            'ch'=> 'Chinese',
            'th'=> 'Thai',
        ];

        if ($event_type === 'course' || $event_type === 'past_course') {
            $event_language = get_post_meta($id, 'pcc_event_language', true);

            if ($event_language) {
                $event_second_language = get_post_meta($id, 'pcc_event_second_language', true);

                return $event_second_language !== 'none' && $event_second_language !== null ? 
                    $languages[ $event_language ] . ' + ' . $languages[ $event_second_language ] . __(' (Live Transl.)', 'pcc') :
                    $languages[ $event_language ];
            }
        }

        return false;
    }

    public function eventTypeLabel()
    {
        global $id;
        $type = get_post_meta($id, 'pcc_event_type', true);
        $types = [
            'community' => __('Community Event', 'pcc'),
            'conference' => sprintf(
                /* Translators: conference year */
                __('Conference %s', 'pcc'),
                strftime('%Y', (int) get_post_meta($id, 'pcc_event_start', true))
            ),
            'pcc' => __('PCC Event', 'pcc'),
            'icde' => __('ICDE  Event', 'pcc'),
        ];
        return $types[ $type ] ?? false;
    }

    public function eventFormat()
    {
        global $id;

        $formats = [
            'async' => __('Online Asynchronous', 'pcc'),
            'sync' => __('Online Synchronous', 'pcc'),
            'async_sync' => __('Online Asynchronous and Synchronous', 'pcc'),
        ];

        $event_format = get_post_meta($id, 'pcc_event_format', true);

        if ($event_format === 'not_online' || $event_format == null) {
            return $this->eventVenue();
        }

        return $formats[ $event_format ];
    }

    public function eventVenue()
    {
        global $id;
        $addressFormatRepository = new AddressFormatRepository();
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository();
        $formatter = new DefaultFormatter($addressFormatRepository, $countryRepository, $subdivisionRepository);

        $venue_name = get_post_meta($id, 'pcc_event_venue', true);
        $venue_street_address = get_post_meta($id, 'pcc_event_venue_street_address', true);
        $venue_locality = get_post_meta($id, 'pcc_event_venue_locality', true);
        $venue_region = get_post_meta($id, 'pcc_event_venue_region', true);
        $venue_postal_code = get_post_meta($id, 'pcc_event_venue_postal_code', true);
        $venue_country = get_post_meta($id, 'pcc_event_venue_country', true);
        if (!$venue_country) {
            $venue_country = 'US';
        };
        $address = new Address();
        $address = $address
            ->withOrganization(nl2br($venue_name))
            ->withAddressLine1($venue_street_address)
            ->withLocality($venue_locality)
            ->withAdministrativeArea($venue_region)
            ->withPostalCode($venue_postal_code)
            ->withCountryCode($venue_country);

        return $formatter->format($address, ['html_attributes' => ['translate' => 'no', 'class' => 'address']]);
    }

    public static function registrationLink($id = 0)
    {
        if (!$id) {
            $id = get_the_ID();
        }
        return get_post_meta($id, 'pcc_event_registration_url', true);
    }

    public function eventSponsors()
    {
        $sponsors = (array) get_post_meta(get_the_ID(), 'pcc_event_sponsors', true);
        return array_filter($sponsors);
    }

    public function eventClasses()
    {
        $classes = (array) get_post_meta(get_the_ID(), 'pcc_event_classes', true);
        return array_filter($classes);
    }

    public function eventInstructorType()
    {
        return get_post_meta(get_the_ID(), 'pcc_event_instructor_type', true);
    }

    public function eventTimezone()
    {
        return get_post_meta(get_the_ID(), 'pcc_event_timezone', true);
    }

    public function eventRibbon()
    {
        global $post, $wp;

        $event_type = $this->eventType();

        if ($post->post_parent > 0) {
            $event_type = get_post_meta($post->post_parent, 'pcc_event_type', true);
        }

        if ($event_type === 'course' || $event_type === 'past_course') {
            $event_children_ribbons = [];

            if ($this->userCanAccess()) {
                $children_events = get_children([
                    'order'          => 'DESC',
                    'post_parent'    => $post->post_parent > 0 ? $post->post_parent : $post->ID,
                    'post_type' => 'pcc-event',
                    'numberposts' => -1,
                ]);
    
                foreach ($children_events as $index => $child_event) {
                    $event_children_ribbons[] = [
                        'class' => ($post->post_parent) ? 'parent' : '',
                        'link' => get_permalink($child_event->ID),
                        'label' => $index === array_key_first($children_events) ? __('Private', 'pcc') : $child_event->post_title,
                    ];
                }
            }
            
            return [
                [
                    'class' => false,
                    'rel' =>
                        (
                            !$post->post_parent &&
                            !isset($wp->query_vars['private'])
                        ) ?
                        'current' :
                        false,
                    'link' => ($post->post_parent) ? get_permalink($post->post_parent) : get_permalink($post),
                    'label' => __('Home', 'pcc'),
                ],
                ...$event_children_ribbons,
            ];
        }

        return [
            [
                'class' => false,
                'rel' =>
                    (
                        !$post->post_parent &&
                        !isset($wp->query_vars['participants']) &&
                        !isset($wp->query_vars['program'])
                    ) ?
                    'current' :
                    false,
                'link' => ($post->post_parent) ? get_permalink($post->post_parent) : get_permalink($post),
                'label' => ($event_type === 'conference') ?
                    __('Conference', 'pcc') :
                    __('Event', 'pcc'),
            ],
            [
                'class' => ($post->post_parent) ? 'parent' : '',
                'rel' => (isset($wp->query_vars['program']) && $wp->query_vars['program'] === 'yes') ?
                    'current' :
                    false,
                'link' => ($post->post_parent) ?
                    get_permalink($post->post_parent) . 'program/' :
                    get_permalink($post) . 'program/',
                'label' => __('Program', 'pcc'),
            ],
            [
                'class' => false,
                'rel' => (isset($wp->query_vars['participants']) &&
                    in_array($wp->query_vars['participants'], ['alphabetical', 'random'], true)) ?
                    'current' :
                    false,
                'link' => ($post->post_parent) ?
                    get_permalink($post->post_parent) . 'participants/' :
                    get_permalink($post) . 'participants/',
                'label' => __('Participants', 'pcc'),
            ],
        ];
    }

    public function eventProgram()
    {
        global $post;
        $program = new \WP_Query([
            'post_type' => 'pcc-event',
            'posts_per_page' => -1,
            'post_parent' => $post->ID,
            'meta_key' => 'pcc_event_start',
            'orderby' => 'meta_value',
            'order' => 'asc',
        ]);
        $tmp = $result = [];
        if ($program->have_posts()) {
            while ($program->have_posts()) {
                $program->the_post();
                $tmp[] = [
                    'datetime' => (int) $program->post->pcc_event_start,
                    'name' => $program->post->name,
                    'session'  => $program->post,
                ];
            }
            wp_reset_postdata();
        }

        $datetime = array_column($tmp, 'datetime');
        $name = array_column($tmp, 'name');

        array_multisort($datetime, SORT_ASC, $name, SORT_ASC, $tmp);

        $previous_day = false;
        foreach ($tmp as $v) {
            $session = $v['session'];
            $day = strftime('%A, %B %e, %Y', $session->pcc_event_start);
            if ($previous_day && $previous_day === $day) {
                $result[ $previous_day ][ $session->post_name ] = $session;
            } else {
                $result[ $day ][ $session->post_name ] = $session;
            }
            $previous_day = $day;
        }
        return $result;
    }

    public function userCanAccess()
    {
        if (!$this->isPaidEvent()) return true;

        if (!is_user_logged_in()) return false;

        $user_is_external_user = user_has_role('external_user');
        $user_has_event = $this->userPurchasedEvent();

        return !$user_is_external_user || ($user_is_external_user && $user_has_event);
    }

    public function isPaidEvent()
    {
        global $post;

        $event_id = $post->post_parent ? $post->post_parent : $post->ID;

        return get_post_meta($event_id, 'pcc_event_oc_paid_event', true);
    }

    public function userPurchasedEvent()
    {
        global $post;

        $user_id = get_current_user_id();
        $event_id = $post->post_parent ? $post->post_parent : $post->ID;

        $user_event_ids = get_user_meta($user_id, 'event_ids', true);
        if (is_array($user_event_ids)) {
            return in_array($event_id, $user_event_ids);
        }
        return false;
    }

    public static function isUpcoming()
    {
        global $post;
        if (time() < (int) get_post_meta($post->id, 'pcc_event_start', true)) {
            return true;
        }

        return false;
    }
}
