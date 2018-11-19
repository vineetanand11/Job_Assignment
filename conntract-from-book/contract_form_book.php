<?php 
	/*
    Plugin Name: Contract Form Book
    Plugin URI: #
    Description: Plugin for 
    Author: Vineet Anand
    Version: 1.0
    Author URI:#
    */
   
	// function to create the DB / Options / Defaults					
	function plugin_options_install() {
	   	global $wpdb;
 		$table_name = $wpdb->prefix . 'book_ticket';
		// create the ECPT metabox database table
		$field = '';
		for ($i=1; $i < 101; $i++) { 
			$field .= '`field_'.$i.'` varchar(50),';
		}
		
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
		{
			$sql = "CREATE TABLE " . $table_name . " (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			".$field."
			UNIQUE KEY id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			save_book_data();
		}

	 
	}
	// run the install scripts upon plugin activation
	register_activation_hook(__FILE__,'plugin_options_install');
	//register_deactivation_hook(__FILE__,'plugin_options_install');
	
 	//Save Ticket Data
	function save_book_data(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'book_ticket';
		$field_arr = array();
		for ($i=1; $i < 101; $i++) { 
			$field_arr['field_'.$i.''] = 0;
		}
		$wpdb->insert($table_name, $field_arr);
	}

    // used for tracking error messages
	function portal_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
	// displays error messages from form submissions
	function portal_show_error_success_messages() {
		if($codes = portal_errors()->get_error_codes()) {
			echo '<div class="job_errors">';
			    // Loop error codes and display errors
			   foreach($codes as $code){
			        $message = job_errors()->get_error_message($code);
			        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.$message.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			    }
			echo '</div>';
		}	
	}