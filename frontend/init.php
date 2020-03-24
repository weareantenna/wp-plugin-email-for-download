<?php

namespace Antenna\EmailForDownload\Frontend;

use Antenna\EmailForDownload\Inc\Crypter;
use Antenna\EmailForDownload\Inc\Mailchimp;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 */
class Init
{

    /** @var string */
    private $plugin_name;

    /** @var string */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/styles.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/scripts.js', array('jquery'), $this->version, false);
    }

    /**
     * Handle the form
     */
    public function handle_form()
    {
        if (!wp_verify_nonce($_POST['email_for_download_nonce'], 'email_for_download_nonce'))
            die('Failed security check');

        if (!is_email($_POST['email_for_download_email'])) {
            wp_redirect(add_query_arg('validation[mail]', $_POST['email_for_download_email'], get_permalink($_POST['email_for_download_page_id'])));
        }

        $crypter = new Crypter();

        $attachment_id   = $crypter->decrypt($_POST['email_for_download_attachment_id']);
        $attachment_path = get_attached_file($attachment_id);

        $mailchimp = new Mailchimp();
        if (!getenv('MC_API_KEY') || $mailchimp->subscribe($_POST['email_for_download_email'], basename($attachment_path))) {
            wp_redirect($this->force_download($attachment_path));
            exit();
        }

        wp_redirect(add_query_arg('validation[mail]', $_POST['email_for_download_email'], get_permalink($_POST['email_for_download_page_id'])));
        exit();
    }

    /**
     * @param $file_path
     */
    public function force_download($file_path)
    {

        $file_name = basename($file_path);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        readfile($file_path);
        exit();
    }
}