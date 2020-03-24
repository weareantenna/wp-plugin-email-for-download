<?php

namespace Antenna\EmailForDownload\Inc;

class Mailchimp
{
    /** @var string */
    private $api_key;

    /** @var string */
    private $list_id = null;

    /** @var array */
    private $tags    = [];

    /**
     * Mailchimp constructor.
     */
    public function __construct()
    {
        $this->api_key = getenv('MC_API_KEY');
        $this->list_id = getenv('MC_LIST_ID');
        $this->tags    = explode(',', getenv('MC_TAGS'));
    }

    /**
     * @param $email
     * @param $file
     *
     * @return bool
     */
    public function subscribe($email, $file)
    {
        $status   = 'subscribed'; // subscribed, cleaned, pending
        $args     = array(
            'method'  => 'PUT',
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode('user:' . $this->api_key)
            ),
            'body'    => [
                'email_address' => $email,
                'status'        => $status,
                'tags'          => array_merge($this->tags, [$file])
            ]
        );

        $response = wp_remote_post('https://' . substr($this->api_key, strpos($this->api_key, '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $this->list_id . '/members/' . md5(strtolower($email)), $args);

        $body = json_decode($response['body']);

        if ($response['response']['code'] == 200 && $body->status == $status) {
            return true;
        }

        return false;
    }
}