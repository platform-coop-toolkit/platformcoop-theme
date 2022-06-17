<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CustomFields
{
    public function __construct() {
        add_action('after_setup_theme', array($this, 'load_carbon_fields'));
        add_action('carbon_fields_loaded', array($this, 'load_fields'));
    }


    public function load_carbon_fields() {
        require_once get_template_directory() . '/../vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }

}

new CustomFields();