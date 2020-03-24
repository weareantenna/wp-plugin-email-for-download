<?php

namespace Antenna\EmailForDownload\Inc;

class I18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            'email-for-download',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }



}