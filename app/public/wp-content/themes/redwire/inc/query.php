<?php

/**
 * Custom query hooks
 */

add_action('elementor/query/featured_posts', function( $query ) {
    $query->set('meta_key', 'featured');
    $query->set('meta_value', '1');
});
