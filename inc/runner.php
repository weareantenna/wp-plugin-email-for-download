<?php

namespace Antenna\EmailForDownload\Inc;

use Antenna\EmailForDownload\Admin\Init as AdminInit;
use Antenna\EmailForDownload\Frontend\Init as FrontendInit;
use Antenna\EmailForDownload\Frontend\Shortcodes\EmailForDownload as EmailForDownloadShortcode;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class Runner
{

    /** @var EmailForDownloadLoader */
    protected $loader;

    /** @var string */
    protected $plugin_name;

    /** @var string */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     */
    public function __construct()
    {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'email-for-download';

        $this->load_dependencies();
        $this->set_locale();
        // $this->define_admin_hooks();
        $this->define_frontend_hooks();
        $this->define_shortcodes();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'inc/loader.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'inc/i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'inc/mailchimp.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'inc/crypter.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/init.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/init.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/shortcodes/email-for-download.php';

        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() : void
    {

        $plugin_i18n = new I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() : void
    {

        $plugin_admin = new AdminInit($this->get_plugin_name(), $this->get_version());
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_frontend_hooks() : void
    {

        $plugin_public = new FrontendInit($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        // $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('admin_post_email_for_download_handle_form', $plugin_public, 'handle_form');
        $this->loader->add_action('admin_post_nopriv_email_for_download_handle_form', $plugin_public, 'handle_form');
    }

    /**
     * Register all of the shortcodes
     */
    private function define_shortcodes()
    {

        $plugin_shortcodes = new EmailForDownloadShortcode();

        $this->loader->add_shortcode('email_for_download', $plugin_shortcodes, 'email_for_download_shortcode');
    }

    /**
     * Add all necessities
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * @return string
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }

}