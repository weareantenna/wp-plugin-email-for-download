<?php

/**
 * Plugin Name: Email for download
 * Version: 0.0.1
 * Description: Creates a form shortcode for downloading files after email is submitted
 *
 * Author: Antenna
 * Author URI: https://weareantenna.be
 *
 * Text Domain: email-for-download
 *
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// TODO
// - add filter for field classes
// - check nonce (done)
// - add settings ui
// - redirect url argument
// - sanitize user input (done)
// - set everything to private
// - remove jquery dep (done)
// - extra fields?
// - validation (done)
// - automatic deploy (plugins.weareantenna.be)

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );


// require the dependencies
require plugin_dir_path( __FILE__ ) . 'inc/runner.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_email_for_download() {

    $plugin = new \Antenna\EmailForDownload\Inc\Runner();
    $plugin->run();

}
run_email_for_download();


