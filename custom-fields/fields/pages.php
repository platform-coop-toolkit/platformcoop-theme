<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function pages() {
    $uncat_categories = get_terms([
        'taxonomy' => 'category',
        'name' => 'Uncategorized',
        'hide_empty' => true,
    ]);
    $uncat_categories = array_column($uncat_categories, 'term_id');

    $args = [
        'hide_empty' => 1,
        'exclude' => array_merge([1], $uncat_categories),
    ];
    $categories = array_column(get_categories($args), 'name', 'term_id');

    Container::make('post_meta', __('Custom fields'))
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'views/page-custom-blog.blade.php')
        ->add_fields([
            Field::make('select', 'category', __('Select a category'))
                ->set_options($categories)
        ]);
}