<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class Page extends Controller
{
    use Partials\Story;

    public function photoCredits()
    {
        if (basename(get_page_template()) !== 'page-photo-credits.blade.php') {
            return;
        }
        $photos = get_posts([
            'post_type' => 'attachment',
            'numberposts' => '-1',
            'order' => 'asc',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key'     => 'pcc_attachment_creator_name',
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => 'pcc_attachment_organization_name',
                    'compare' => 'EXISTS',
                ],
            ],
        ]);
        foreach ($photos as $k => $photo) {
            $pcc_attachment_creator_name = get_post_meta($photo->ID, 'pcc_attachment_creator_name', true);
            $pcc_attachment_creator_link = get_post_meta($photo->ID, 'pcc_attachment_creator_link', true);
            $pcc_attachment_organization_name = get_post_meta($photo->ID, 'pcc_attachment_organization_name', true);
            $pcc_attachment_organization_link = get_post_meta($photo->ID, 'pcc_attachment_organization_link', true);
            if ($pcc_attachment_creator_name &&
                $pcc_attachment_organization_name) {
                if ($pcc_attachment_creator_link) {
                    $creator = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        $pcc_attachment_creator_link,
                        $pcc_attachment_creator_name
                    );
                } else {
                    $creator = $pcc_attachment_creator_name;
                }
                if ($pcc_attachment_organization_link) {
                    $organization = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        $pcc_attachment_organization_link,
                        $pcc_attachment_organization_name
                    );
                } else {
                    $organization = $pcc_attachment_organization_name;
                }
                $photo->credit = sprintf(
                    __('Photo by %1$s for %2$s', 'pcc'),
                    $creator,
                    $organization
                );
            } elseif ($pcc_attachment_creator_name) {
                if ($pcc_attachment_creator_link) {
                    $creator = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        $pcc_attachment_creator_link,
                        $pcc_attachment_creator_name
                    );
                } else {
                    $creator = $pcc_attachment_creator_name;
                }
                $photo->credit = sprintf(
                    __('Photo by %1$s', 'pcc'),
                    $creator
                );
            } elseif ($pcc_attachment_organization_name) {
                if ($pcc_attachment_organization_link) {
                    $organization = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        $pcc_attachment_organization_link,
                        $pcc_attachment_organization_name
                    );
                } else {
                    $organization = $pcc_attachment_organization_name;
                }
                $photo->credit = sprintf(
                    __('Photo by %1$s', 'pcc'),
                    $organization
                );
            }
        }
        return $photos;
    }

    /*
    Create a new Wordpress Query for looping `pcc-story` post types.
    */
    public function storiesQuery()
    {
        $args = [
            'post_type' => 'pcc-story',
            'posts_per_page' => -1,
            'orderby' => 'post_date',
            'order' => 'desc',
        ];

        /* If the clear parameter is set, unset all parameters so they
        aren't queried. */
        if (get_query_var('clear')) {
            /* TODO: Improve this implementation. Parameters should
            be unset on submission and before reaching this point. */
            remove_query_arg('org');
            remove_query_arg('clear');
        } elseif (get_query_var('org')) {
            /* If filtering by a single organization name. */
            $args ['tax_query'][] = [
                'taxonomy' => 'pcc-organization',
                'field' =>'name',
                'terms' => get_query_var('org')
            ];
        }

        $query = new \WP_Query($args);
        return $query;
    }

    /*
    Return an array of unique organization names sorted alphabetically,
    false if there are no organizations.
    */
    public function storyOrgs()
    {
        $terms = get_terms('pcc-organization');

        if ($terms && ! is_wp_error($terms)) {
            foreach ($terms as $term) {
                $results[] = $term->name;
            }

            $results = array_unique($results);
            sort($results);

            return $results;
        }

        return false;
    }

    /*
    Create a link list for navigating Stories by taxonomy terms. The first link
    of the list is a link to the Community Stories page which lists all stories.
    Returns false if there are no terms, or the supplied taxonomy name is
    invalid.
    */
    public static function taxonomyMenuList($taxonomy = false)
    {
        $output = '';
        if ($taxonomy) {
            $terms = get_terms($taxonomy);

            if ($terms && ! is_wp_error($terms)) {
                $li_class = 'link-list__item';
                $output .= '<ul class="link-list">';

                // If on a taxonomy page, put a link back to the Stories page.
                if (is_tax()) {
                    $output .= '<li class="link-list__item"><a href="'
                        . get_permalink(get_page_by_title('Community Stories')->ID)
                        . '">' . __('All', 'pcc') .'</a></li>';
                }

                foreach ($terms as $term) {
                    $link = get_term_link($term->term_id);
                    $aria_current = '';

                    if (strcmp(single_term_title('', false), $term->name) == 0) {
                        $aria_current = ' aria-current="true"';
                    }

                    $output .= '<li class="link-list__item">';
                    $output .= '<a href="'.$link.'"'.$aria_current.'>'.$term->name.'</a>';
                    $output .= '</li>';
                }
                $output .= '</ul>';

                return $output;
            }
        }

        return false;
    }

    public function councilQuery()
    {
        return Page::peopleQuery('member-institute-council-of-advisors');
    }

    public function staffQuery()
    {
        return Page::peopleQuery('staff');
    }

    public function researchFellowsQuery()
    {
        return Page::peopleQuery('research-fellow-institute');
    }

    public function affiliateFacultyQuery()
    {
        return Page::peopleQuery('affiliate-faculty-institute');
    }

    public function studentFellowsQuery()
    {
        return Page::peopleQuery('student-fellow');
    }

    public function researchAffiliateInstituteQuery()
    {
        return Page::peopleQuery('research-affilliate-institute');
    }
    
    public function affiliatedAdvisorsInstituteQuery()
    {
        return Page::peopleQuery('affiliated-advisors-institute');
    }

    public static function peopleQuery($role = false)
    {
        if ($role) {
            $query = new \WP_Query(
                [
                    'post_type' => 'pcc-person',
                    'posts_per_page' => -1,
                    'tax_query' => [
                        [
                            'taxonomy' => 'pcc-role',
                            'field' => 'slug',
                            'terms' => $role,
                        ],
                    ],
                    'orderby' => 'post_title',
                    'order' => 'asc',
                ]
            );
            return $query;
        }
        return false;
    }
}
