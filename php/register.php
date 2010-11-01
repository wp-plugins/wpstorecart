<?php

error_reporting(0);

define('WP_USE_THEMES', false);
require('../../../../wp-blog-header.php');
require_once( ABSPATH . WPINC . '/registration.php');
if('POST' != $_SERVER['REQUEST_METHOD'])die('not post');
$user_login = sanitize_user($_POST['email']);
$user_email=$_POST['email'];
$user_pass = $_POST['user_pass'];
$redirect_to = $_POST['redirect_to'];
if(username_exists( $user_login ) || !validate_username( $user_login ) || !is_email( $user_email ) || email_exists( $user_email ))die('error');
$user_id = wp_create_user( $user_login, $user_pass, $user_email );
if ( !$user_id )die('bad user_id');
wp_new_user_notification($user_id, $user_pass);
$credentials=array('remember'=>true,'user_login'=>$user_login,'user_password'=>$user_pass);
do_action_ref_array('wp_authenticate', array(&$credentials['user_login'], &$credentials['user_password']));
$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
wp_set_auth_cookie($user_id, $credentials['remember']);
do_action('wp_login', $credentials['user_login']);

update_usermeta($user_id, 'state', $_POST['wpstate'] );

wp_safe_redirect($redirect_to);
exit();
?>