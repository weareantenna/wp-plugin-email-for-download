<?php

namespace Antenna\EmailForDownload\Frontend\Shortcodes;

use Antenna\EmailForDownload\Inc\Crypter;

class EmailForDownload
{

    private $attributes     = null;
    private $attachment_id  = null;

    public function email_for_download_shortcode($atts = [])
    {
        global $post;

        $attributes = shortcode_atts(array(
            'attachment_id'        => null,
            'redirect_url'         => get_permalink($post->ID),
            'page_id'              => $post->ID,
            'form_container_class' => 'form',
        ), $atts, 'email_for_download');

        if (!$attributes['attachment_id'])
            return '';

        $this->attributes = $attributes;

        return $this->email_for_download_render_form();
    }

    public function email_for_download_render_form()
    {
        $crypter = new Crypter();

        $validators = ['mail' => null];
        if (array_key_exists('validation', $_GET)) {
            $validators = $_GET['validation'];
        }

        $output = '<div id="email_for_download_container" class="efd-container ' . $this->attributes['form_container_class'] . '">';
        $output .= '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="POST" class="efd-form">';
        $output .= '<input type="text" name="email_for_download_email" value="' . $validators['mail'] . '" class="form-control efd-field efd-email' . (array_key_exists('mail', $validators) ? ' error' : '') . '" placeholder="E-mail" required>';
        $output .= '<button type="submit" name="email_for_download_submit" class="form__btn btn btn-link efd-button efd-submit">Verzenden</button>';
        $output .= '<input type="hidden" name="email_for_download_attachment_id" value="' . $crypter->encrypt($this->attributes['attachment_id']) . '">';
        $output .= '<input type="hidden" name="email_for_download_redirect_url" value="' . $this->attributes['redirect_url'] . '">';
        $output .= '<input type="hidden" name="email_for_download_page_id" value="' . $this->attributes['page_id'] . '">';
        $output .= '<input type="hidden" name="action" value="email_for_download_handle_form">';
        $output .= '<input type="hidden" name="email_for_download_nonce" value="' . wp_create_nonce('email_for_download_nonce') . '">';
        $output .= '</form>';
        $output .= '</div>';

        return $output;
    }
}