# Email for download

This plugin is a **work in progress** plugin that enables anybody to embed a form with a single
input, the email.

A valid form submission will add the the contact to mailchimp and force download a file

## Usage

### Shortcode
The form is added throughout a shortcode

```
[email_for_download attachment_id="1382" form_container_class="form form--light"]
```

This shortcode needs 2 attributes

* Required - Attachment id, as you might presume: the attachment that should be prompted
* Optional - Form container class, the extra class that is added to the form container


### Settings

The settings for the Mailchimp configuration are now added within a .env file. We presume your project can handle
this and use any variable within the .env file.

* MC_API_KEY=xxx
* MC_LIST_ID=xxx
* MC_TAGS="aaa,xxxx" - This can be comma separated


## The future

This plugin is work in progress. The following things would be great to be added

* Multipe fields and Mailchimp field mapping
* Admin configuration page (for Mailchimp settings & others)
* Multilingual options
* Button above the wysiwyg to include the shortcode with given attributes

