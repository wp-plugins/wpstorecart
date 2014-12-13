<?php

/**
 * Sets up wpStoreCart to use multiple languages
 */
function wpscLanguageInit() {
    load_plugin_textdomain( 'wptorecart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('init', 'wpscLanguageInit');

?>