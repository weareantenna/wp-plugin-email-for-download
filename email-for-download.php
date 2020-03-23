<?php
/*
Plugin Name: Email for download
Version: 0.0.1
Description: Creates a form shortcode for downloading files after email is submitted
Author: Frederik Van Den Berge
Text Domain: email-for-download
*/

// TODO
// - add filter for field classes
// - check nonce
// - add settings ui
// - redirect url argument
// - sanitize user input
// - set everything to private
// - remove jquery dep
// - extra fields?
// - validation
// - automatic deploy (plugins.weareantenna.be)

class EmailForDownload {
	private $attributes = null;
	private $attachment_id = null;
	private $secret_key = 'efd_secret_key';
	private $secret_iv = 'efd_secret_iv';
	private $encrypt_method = 'AES-256-CBC';

	public function __construct() {

		// Enqueue assets
		add_action('wp_enqueue_scripts', array($this, 'efd_enqueue_scripts'));

		// Register shortcode
		add_shortcode('email_for_download', array($this, 'efd_create_shortcode'));

		// Handle form submission
		add_action('admin_post_efd_handle_form', array($this, 'efd_handle_form'));
		add_action('admin_post_nopriv_efd_handle_form', array($this, 'efd_handle_form'));

		// Add download form to footer
		add_action('wp_footer', array($this, 'efd_append_download_form'));

		add_action('admin_post_efd_force_download', array($this, 'efd_force_download'));
		add_action('admin_post_nopriv_efd_force_download', array($this, 'efd_force_download'));
	}

	public function efd_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('efd_scripts',  plugin_dir_url( __FILE__ ) . 'js/scripts.js', 'jquery', false, true);
	}

	public function efd_create_shortcode($atts) {
		global $post;

		// Get shortcode attributes
		$attributes = shortcode_atts( array(
			'attachment_id' => null,
			'redirect_url' => get_permalink($post->ID),
			'form_container_class' => 'form',
   		), $atts, 'email_for_download' );

		// file_url argument is required - abort if not provided
   		if(!$attributes['attachment_id'])
   			return $this->efd_render_validation_error();

   		// if(!$attributes['redirect_url']){
   		// 	$this->attributes['same_page'] = true;
   		// 	$this->attributes['redirect_url'] = get_permalink($post->ID);
   		// }

   		// Set properties
   		$this->attributes = $attributes;

   		// render efd form
   		return $this->efd_render_form();
	}

	public function efd_render_form() {
		global $post;

		$output = '<div id="efd_container" class="efd-container '. $this->attributes['form_container_class'] .'">';
		$output .= '<form action="'. esc_url( admin_url( 'admin-post.php' ) ) .'" method="POST" class="efd-form">';
		$output .= '<input type="email" name="efd_email" class="form-control efd-field efd-email" placeholder="E-mail" required>';
		$output .= '<button type="submit" name="efd_submit" class="form__btn btn btn-link efd-button efd-submit">Verzenden</button>';
		$output .= '<input type="hidden" name="efd_attachment_id" value="'.$this->efd_encrypt($this->attributes['attachment_id']).'">';
		$output .= '<input type="hidden" name="efd_redirect_url" value="'. $this->attributes['redirect_url'] .'">';
		$output .= '<input type="hidden" name="efd_page_id" value="'. $post->ID .'">';
		$output .= '<input type="hidden" name="action" value="efd_handle_form">';
		$output .= '<input type="hidden" name="efd_nonce" value="'. wp_create_nonce('edf_nonce') .'">';
		$output .= '</form>';
		$output .= '</div>';

		return $output;
	}

	public function efd_handle_form() {

		// TODO
		// Check email
		// Sanitize input
		// Connect to mailchimp
		// Send email?
		// check wp_redirect hash to jump to form feedback

		// Add hash to url if redirect is same page
		if( $_POST['efd_redirect_url'] == get_permalink($_POST['efd_page_id'] ))
			wp_redirect(add_query_arg('efd_download', $_POST['efd_attachment_id'], $_POST['efd_redirect_url'] . '#efd_container'));

	    wp_redirect(add_query_arg('efd_download', $_POST['efd_attachment_id'], $_POST['efd_redirect_url']));
	    exit();
	}

	public function efd_append_download_form() {

		$form = '<form action="'. esc_url( admin_url( 'admin-post.php' ) ) .'" method="POST" id="efd_force_download">';
		$form .= '<input type="hidden" name="efd_attachment_id" value="'. ( isset($_GET['efd_download']) ? $_GET['efd_download'] : '') .'">';
		$form .= '<input type="hidden" name="action" value="efd_force_download">';
		$form .= '<input type="hidden" name="efd_nonce" value="'. wp_create_nonce('edf_nonce') .'">';
		$form .= '</form>';
		echo $form;
	}

	public function efd_force_download() {

		$attachment_id = $this->efd_decrypt($_POST['efd_attachment_id']);
		$attachment_path = get_attached_file($attachment_id);

		$file_name = basename($attachment_path);

		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$file_name");
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: binary");
		readfile($attachment_path);
	}

	public function efd_render_validation_error() {

		// Don't show validation messages in public
		if(!is_user_logged_in()) return;

		return '<div class="alert">Please enter the required file_url argument</div>';
	}

	private function efd_encrypt($string) {
	    $key = hash( 'sha256', $this->secret_key );
	    $iv = substr( hash( 'sha256', $this->secret_iv ), 0, 16 );
	    return base64_encode( openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv ) );
	}

	private function efd_decrypt($string) {
		$key = hash( 'sha256', $this->secret_key );
	    $iv = substr( hash( 'sha256', $this->secret_iv ), 0, 16 );
		return openssl_decrypt( base64_decode($string), $this->encrypt_method, $key, 0, $iv );
	}

}

$EmailForDownload = new EmailForDownload();