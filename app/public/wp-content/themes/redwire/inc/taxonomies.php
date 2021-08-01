<?php

/**
 * Custom taxonomies
 */

add_action('init', function() {
    // Business Unit
    register_taxonomy( "business_unit", array("product"),
        array(
            "label" => "Business Units",
            "labels" => array(
                "name" =>  "Business Units",
                "singular_name" =>  "Business Unit",
            ),
            "public" => true,
            "hierarchical" => false,
            "label" => "Business Units",
            "show_ui" => true,
            "query_var" => true,
            "show_in_rest" => true,
            "rewrite" => array(
                "slug" => "business-unit",
                "with_front" => false,
            )
        )
    );
});
