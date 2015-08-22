<?php
/**
Plugin Name: LH Email Registration
Plugin URI: http://wordpress.org/plugins/lh-email-registration/
Description: Streamlines user registration in the backend by removing redundant fields and replaces usernames with email adresses
Version: 2.0
Author: Peter Shaw
Author URI: http://shawfactor.com
License: GPLv2 or later
**/
/*  Copyright 2013-2014   Peter Shaw  (email : pete@localhero.biz)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class LH_email_registration_plugin {

var $opt_name = "lh_email_registration-options";
var $hidden_field_name = 'lh_email_registration-submit_hidden';
var $use_email_field_name = 'lh_email_registration-use_email';
var $remove_url_field_name = 'lh_email_registration-remove_url';
var $password_email_field_name = 'lh_email_registration-password_email';
var $options;
var $filename;

public function reconfigure_fields_by_js() {

?>
<script>

jQuery(document).ready(function(){


<?php  if ($this->options[$this->use_email_field_name] == 1){?>


function lh_users_generateRandom() {
    var length = 8,
        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}

password = lh_users_generateRandom();

jQuery('#user_login').val(password);

jQuery('#user_login').parents('tr').hide();




<?php }  ?>




<?php  if ($this->options[$this->remove_url_field_name] == 1){?>jQuery('#url').parents('tr').remove();   <?php }  ?>


<?php  if ($this->options[$this->password_email_field_name] == 1){ 
?>

jQuery('#pass1').parents('tr').html('<th scope="row"><label for="pass1">Password<span class="description hide-if-js">(required)</span></label></th><td><input class="hidden" value=" " /><!-- #24364 workaround --><button type="button" class="button button-secondary wp-generate-pw hide-if-no-js">Show password</button><div class="wp-pwd hide-if-js"><span class="password-input-wrapper"><input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="*kxDlyekB2NYwEzrU0EUYjAR" aria-describedby="pass-strength-result" /></span><button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Hide password"><span class="dashicons dashicons-hidden"></span><span class="text">Hide</span></button><button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="Cancel password change"><span class="text">Cancel</span></button><div style="display:none" id="pass-strength-result" aria-live="polite"></div></div><p><span class="description">Send the new user the generated password by email? <input type="checkbox" name="send_password" id="send_password" value="1"  /></span></p></td>');



<?php }  ?>

});

</script>

<?php



 
}

public function override_usernames_on_save( $user_id ) {

global $wpdb;

if ($this->options[$this->use_email_field_name] == 1){

$sql = "update ".$wpdb->prefix."users set user_login = user_email where ID = '".$user_id."'";

$result = $wpdb->get_results($sql);

}

}



public function run_wp_loaded(){

//get rid of actions on user new form we want this as simple as possible

remove_all_actions( 'user_new_form');

}


public function wp_redirect_after_user_new( $location ){
    global $pagenow;

    if( is_admin() && 'user-new.php' == $pagenow ) {
        $user_details = get_user_by( 'email', $_REQUEST[ 'email' ] );
        $user_id = $user_details->ID;

        if( $location == 'users.php?update=add&id=' . $user_id )
            return add_query_arg( array( 'user_id' => $user_id ), 'user-edit.php' );
    }

    return $location;
}



function plugin_menu() {
add_options_page('LH Email Registration', 'LH Email Registration', 'manage_options', $this->filename, array($this,"plugin_options"));

}

function plugin_options() {

if (!current_user_can('manage_options')){

wp_die( __('You do not have sufficient permissions to access this page.') );

}

if( isset($_POST[ $this->hidden_field_name ]) && $_POST[ $this->hidden_field_name ] == 'Y' ) {

        // Read their posted value


if (($_POST[$this->use_email_field_name] == "0") || ($_POST[$this->use_email_field_name] == "1")){
$options[$this->use_email_field_name] = $_POST[ $this->use_email_field_name ];
}

if (($_POST[$this->remove_url_field_name] == "0") || ($_POST[$this->remove_url_field_name] == "1")){
$options[$this->remove_url_field_name] = $_POST[ $this->remove_url_field_name ];
}

if (($_POST[$this->password_email_field_name] == "0") || ($_POST[$this->password_email_field_name] == "1")){
$options[$this->password_email_field_name] = $_POST[ $this->password_email_field_name ];
}


if (update_site_option( $this->opt_name, $options )){

$this->options = get_site_option($this->opt_name);

?>
<div class="updated"><p><strong><?php _e('User registration settings saved', 'menu-test' ); ?></strong></p></div>
<?php


}


}


  // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

echo "<h1>" . __('LH Email Registration', 'menu-test' ) . "</h1>";

    // settings form
    
    ?>

<form name="lh_email_registration-backend_form" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y" />

<p><label for="<?php echo $this->use_email_field_name; ?>"><?php _e("Use only emails:", 'menu-test' ); ?></label>
<select name="<?php echo $this->use_email_field_name; ?>" id="<?php echo $this->use_email_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->use_email_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->use_email_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select> - Set this to yes if you want your users usernames to be automatically set the same as their email
</p>

<p><label for="<?php echo $this->remove_url_field_name; ?>"><?php _e("Remove URL field:", 'menu-test' ); ?></label>
<select name="<?php echo $this->remove_url_field_name; ?>" id="<?php echo $this->remove_url_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->remove_url_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->remove_url_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select> - Set this to yes to hide the url field in the new user set up
</p>

<p><label for="<?php echo $this->password_email_field_name; ?>"><?php _e("Make password email optional:", 'menu-test' ); ?></label>
<select name="<?php echo $this->password_email_field_name; ?>" id="<?php echo $this->password_email_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->password_email_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->password_email_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select> - Set this to yes if you want too make sending the email to new users optional
</p>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>



</div>

<?php

}

// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}

public function return_password_email() {

return $this->options[$this->password_email_field_name];

}


function __construct() {

$this->options = get_site_option($this->opt_name);
$this->filename = plugin_basename( __FILE__ );


add_action('admin_menu', array($this,"plugin_menu"));
add_action( 'wp_loaded', array($this,"run_wp_loaded"));
add_action('admin_head-user-new.php',array($this,"reconfigure_fields_by_js"));
add_action('user_register', array($this,"override_usernames_on_save"), 10, 1 );
add_action('profile_update', array($this,"override_usernames_on_save"), 10, 2 );
add_filter('wp_redirect', array($this,"wp_redirect_after_user_new"), 1, 1 );
add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);


}


}


$lh_email_registration = new LH_email_registration_plugin();


function lh_email_registration_return_password_email(){

$lh_email_registration_again = new LH_email_registration_plugin();

$email = $lh_email_registration_again->return_password_email();

if ($email == 0){

return true;

} else {

if ($_POST['send_password'] == "1"){

return true;

} else {


return false;

}

}

}

// Redefine user notification function

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since 2.0.0
 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
 *
 * @param int    $user_id User ID.
 * @param string $notify  Whether admin and user should be notified ('both') or
 *                        only the admin ('admin' or empty).
 */
function wp_new_user_notification( $user_id, $notify = '' ) {

if (lh_email_registration_return_password_email()){
	global $wpdb;
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( 'admin' === $notify || empty( $notify ) ) {
		return;
	}

	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

	$message .= wp_login_url() . "\r\n";

	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}
}
endif;


?>