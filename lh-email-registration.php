<?php
/**
Plugin Name: LH Email Registration
Plugin URI: http://wordpress.org/plugins/lh-email-registration/
Description: Streamlines user registration in the backend by removing redundant fields and replaces usernames with email adresses
Version: 1.1
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

public function reconfigure_fields_by_js() {

?>
<script>jQuery(document).ready(function(){jQuery('#url').parents('tr').remove();});


function lh_users_generatePassword() {
    var length = 8,
        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}



jQuery(document).ready(function(){

password = lh_users_generatePassword();

jQuery('#pass1').parents('tr').remove();

jQuery('#pass2').parents('tr').remove();

string = '<input type="hidden" name="pass1" value="' + password + '" />';

string2 = '<input type="hidden" name="pass2" value="' + password + '" />';

jQuery('#createuser').append(string);

jQuery('#createuser').append(string2);



jQuery('#pass2').parents('tr').remove();

jQuery('#user_login').val(password);

jQuery('#user_login').parents('tr').hide();


jQuery('#send_password').parents('tr').html('<th scope="row"><label for="send_password">Send Password?</label></th><td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" value="1"  /> Send the new user a randomly generated password by email.</label></td>');



});

</script>

<?php



 
}

public function override_usernames_on_save( $user_id ) {

global $wpdb;

$sql = "update ".$wpdb->prefix."users set user_login = user_email where ID = '".$user_id."'";

$result = $wpdb->get_results($sql);


}



public function run_wp_loaded(){

//get rid of actions on user new form we want this as simple as possible

remove_all_actions( 'user_new_form');

}


public function wp_redirect_after_user_new( $location ){
    global $pagenow;

    if( is_admin() && 'user-new.php' == $pagenow )
    {
        $user_details = get_user_by( 'email', $_REQUEST[ 'email' ] );
        $user_id = $user_details->ID;

        if( $location == 'users.php?update=add&id=' . $user_id )
            return add_query_arg( array( 'user_id' => $user_id ), 'user-edit.php' );
    }

    return $location;
}



function __construct() {

add_action( 'wp_loaded', array($this,"run_wp_loaded"));
add_action('admin_head-user-new.php',array($this,"reconfigure_fields_by_js"));
add_action('user_register', array($this,"override_usernames_on_save"), 10, 1 );
add_action('profile_update', array($this,"override_usernames_on_save"), 10, 2 );
add_filter('wp_redirect', array($this,"wp_redirect_after_user_new"), 1, 1 );


}


}


$lh_email_registration = new LH_email_registration_plugin();



?>