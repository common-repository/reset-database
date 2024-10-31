<?php
/*
Plugin Name: Reset Database
Plugin URI: http://www.maltesesolutions.com
Description: It resets the database to the default installation and will delete any media files.
Version: 1.1.3
Author: MalteseSolutions
Author URI: http://www.maltesesolutions.com
Text Domain: reset-database
*/

if ( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed this file directly
} 

// This is restricted to administrators
if (is_admin()) {

	// Create the class
	class ResetDatabase {

		function __construct() {

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'admin_init', array( $this, 'process_reset' ) );

			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_shortcut') );

		}

		public function admin_menu() {

			// This Menu will be in "Tools Menu"
			add_management_page( 
		
				'Reset Database',
		
				'Reset Database',
		
				'manage_options',
		
				'reset-database',
		
				array( $this, 'reset_page' )
		
			);

		}

		public function process_reset() {

			global $current_user;

			if(isset($_REQUEST['reset'])) :

				$valid_nonce = ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'reset' ) ) ? true : false;

				$admin = get_user_by( 'login', 'admin' );

				if ( ! isset( $admin->user_login ) || $admin->user_level < 10 ) : $user = $current_user;

					if ( $user && wp_check_password( $_REQUEST['ResetPassword'], $user->data->user_pass, $user->ID)) {

						// Valid Password

						require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

						$blogname = get_option( 'blogname' );

						$admin_email = get_option( 'admin_email' );

						$blog_public = get_option( 'blog_public' );

						// Reset the database to defaults
						global $wpdb;

						$prefix = str_replace( '_', '\_', $wpdb->prefix );

						$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );

						foreach ( $tables as $table ) {

							$wpdb->query( "DROP TABLE $table" );

						}

						$depreciated = '';

						$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public, $depreciated, $user->user_pass );

						extract( $result, EXTR_SKIP );

						$strQuery = "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = %s WHERE id = %d";

						$wpdb->query($wpdb->prepare( $strQuery, $user->user_pass, '', $user_id ) );

						$get_user_meta = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';

						$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

						if ( $get_user_meta( $user_id, 'default_password_nag' ) )

							$update_user_meta( $user_id, 'default_password_nag', false );

						if ( $get_user_meta( $user_id, $wpdb->prefix . 'default_password_nag' ) )

							$update_user_meta( $user_id, $wpdb->prefix . 'default_password_nag', false );
						
						@activate_plugin( plugin_basename( __FILE__ ) );

						wp_clear_auth_cookie();
						
						wp_set_auth_cookie( $user_id );

						// Clear the media directory

						$upload_dir = wp_upload_dir();

						$this->delete_media( $upload_dir['basedir'] );

						wp_redirect(admin_url('index.php'));

						exit();

					} else {

						// Non Valid Password

						add_settings_error(
					
								'myUniqueIdentifyer',
					
								esc_attr( 'settings_updated' ),
					
								'Invalid Password',
					
								'error'
					
							);
						
						wp_redirect(admin_url('tools.php?page=reset-database&error=1'));

						exit();

					}

				endif;

			else :

				return;

			endif;

		}

		public function reset_page() {

			global $current_user;

			echo '<div class="wrap">';

			echo '<div id="icon-tools" class="icon32"><br /></div>';

			echo '<h2>Reset Database</h2>';

			settings_errors();

			// if(isset($_REQUEST['error'])) :

			// 	echo '<div id="message" class="error">Invalid Password!</div>';

			// endif;


			$admin = get_user_by( 'login', 'admin' );

			if ( ! isset( $admin->user_login ) || $admin->user_level < 10 ) : $user = $current_user;

				echo '<p>The user "admin" does not exist.<br/>The user <strong>'. esc_html( $user->user_login ) .'</strong> will be recreated with its <strong>current password</strong>.</p>';

			else :

				echo '<p>The "<strong>admin</strong>" user exists and will be recreated with its <strong>current password</strong>.</p>';

				echo '<p>You should consider changing your admin username to something else for security reasons.</p>';

			endif;

			echo '<p><strong>All other users and administrators will be deleted!</strong></p>';

			echo '</div>';

			echo '<hr>';

			echo '<h3>Reset</h3>';

			echo '<p>Enter your <strong>admin password</strong> to confirm the reset.</p>';

			echo '<form id="reset-form" action="" method="post">';

			wp_nonce_field( 'reset' );

			echo '<input id="reset-database" type="hidden" name="reset" value="true" />';

			echo '<p><input id="reset-password" type="password" name="ResetPassword" value="" maxlength="50" autocomplete="off"/></p>';

			submit_button( 'Reset Database' );

			echo '<hr>';

			echo '<p>This plugin will be reactivated after the reset operation.</p>';

		}

		// Delete the media directory
		public function delete_media( $dirPath ) {

			if (! is_dir( $dirPath ) ) {

				throw new InvalidArgumentException("$dirPath must be a directory");

			}

			if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {

				$dirPath .= '/';

			}

			$files = glob( $dirPath . '*', GLOB_MARK );

			foreach ( $files as $file ) {

				if ( is_dir( $file ) ) {

					$this->delete_media( $file );

				} else {

					@ unlink( $file );
				}

			}

			@ rmdir( $dirPath );

		}


		// Add shortcut to plugin page
		public function plugin_shortcut( $links ) {

			$links[] = '<a href="'. esc_url(get_admin_url(null, 'tools.php?page=reset-database')).'">Reset</a>';

			return $links;

		}

	}

	// Instantiate the Class
	$ResetDatabase = new ResetDatabase();

}