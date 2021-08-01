<?php

use ElementorPro\Plugin;

// Inject Paycor job application script
add_shortcode('careers_application', function ($atts) {
    $preview_mode = Plugin::elementor()->preview->is_preview_mode();
    $edit_mode = Plugin::elementor()->editor->is_edit_mode();

    if (!$preview_mode && !$edit_mode) {
        echo '<script id="gnewtonjs" type="text/javascript" src="//recruitingbypaycor.com/career/iframe.action?clientId=8a7883d0766d99fc0176b0cd67871d57"></script>';
    }
});
