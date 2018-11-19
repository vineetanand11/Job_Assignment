<?php 
	/*
    Plugin Name: Job Assignment
    Plugin URI: #
    Description: Plugin for 
    Author: Vineet Anand
    Version: 1.0
    Author URI:#
    */

 
	add_action('admin_menu', 'add_plugin_page');
    function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Job Settings', 
            'Job', 
            'manage_options', 
            'job-settings', 
            'create_job_admin_page'
        );
    }

    /**
     * Options page callback
     */
    function create_job_admin_page()
    {
        // Set class property
        ?>
        <div class="wrap">
            <h1>Post Type Settings</h1>
            <?php job_show_error_success_messages();
            	$check_post_val = maybe_unserialize(get_option('post_type_check'));
            ?>
            <form method="post" action="">
            	<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="blogname">Post</label></th>
							<td><input name="post_type_title" type="text" value="<?php echo get_option('post_type_title');?>" class="regular-text" ></td>
						</tr>
						<tr>
							<th scope="row"><label for="blogname">Post</label></th>
							<td><input name="post_type_check[]" type="checkbox" value="post" class="regular-text" <?php echo in_array('post', $check_post_val)?'checked':'';?>></td>
						</tr>
						<tr>
							<th scope="row"><label for="blogname">Page</label></th>
							<td><input name="post_type_check[]" type="checkbox" value="page" class="regular-text" <?php echo in_array('page', $check_post_val)?'checked':'';?>></td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'custom_post_settings', 'custom-post-settings-fields' ); ?>
            	<p class="submit"><input type="submit" name="post_check_submit" id="post_check_submit" class="button button-primary" value="Save Changes"></p>
            </form>
        </div>
        <?php
    }
    function save_post_type_settings(){
		// if this fails, check_admin_referer() will automatically print a "failed" page and die.
		if ( ! empty( $_POST['post_check_submit'] ) && check_admin_referer( 'custom_post_settings', 'custom-post-settings-fields' ) ) {
		   // process form data
			if(!empty($_POST['post_type_title'])){
				$option_name = 'post_type_title';
				$option_name_value = $_POST['post_type_title'];
			}else{
				$option_name = 'post_type_title';
				$option_name_value = NULL;
			}

			if(array_key_exists('post_type_check', $_POST)){
				$option_name1 = 'post_type_check';
				$option_name_value1 = maybe_serialize($_POST['post_type_check']);
			}else{
				$option_name1 = 'post_type_check';
				$option_name_value1 = NULL;
			}

			$res_arr = array();
			if ( get_option( $option_name ) !== false ) {
	            update_option( $option_name, $option_name_value );
	            $res_arr[] = 'update';
	        } else {
	            $deprecated = null;
	            $autoload = 'no';
	            add_option( $option_name, $option_name_value, $deprecated, $autoload );
	            $res_arr[] = 'save';
	        }
	        if ( get_option( $option_name1 ) !== false ) {
	            update_option( $option_name1, $option_name_value1 );
	            $res_arr[] = 'update';
	        } else {
	            $deprecated1 = null;
	            $autoload1 = 'no';
	            add_option( $option_name1, $option_name_value1, $deprecated1, $autoload1 );
	            $res_arr[] = 'save';  
	        }
	        if(in_array('update', $res_arr)){
	        	job_errors()->add('Update', __('Setting updated successfully'));
	        }else{
	        	job_errors()->add('Save', __('Setting saved successfully'));
	        }
		}	
    }
    add_action('init','save_post_type_settings');

    function add_stylesheet_script() {
	    wp_register_style('select-min',plugins_url('/job_assignment/lib/css/select2.min.css'));
	    wp_enqueue_style( 'select-min' );
	    wp_register_script( 'jquery-min', plugins_url( '/lib/js/jquery.min.js', __FILE__ ) );
	    wp_enqueue_script( 'jquery-min' );
	    wp_register_script( 'select-min', plugins_url( '/lib/js/select2.min.js', __FILE__ ) );
	    wp_enqueue_script( 'select-min' );
	    wp_enqueue_script('custom-script',plugins_url( '/lib/js/script.js', __FILE__ ),array( 'jquery' ));
	}
	add_action( 'wp_enqueue_scripts', 'add_stylesheet_script' );

    function get_post_by_post_type(){
    	$check_post_data = maybe_unserialize(get_option('post_type_check')); ?>
		<div class="content">
			<h2><?php echo get_option('post_type_title');?></h2>
			<select name="post_type[]" id="show_check_post" multiple="multiple" style="width: 100%;">
				<?php foreach($check_post_data as $check_post_val){?>
					<option value="<?php echo $check_post_val;?>"><?php echo ucwords($check_post_val);?></option>
				<?php } ?>
			</select>
			<div id="post-custom"></div>
		</div>
	<?php 
    }
    add_shortcode('post_by_post_type','get_post_by_post_type');

    function get_post_by_post(){
    	// $check_post_val = maybe_unserialize(get_option('post_type_check'));
    	$post_html = '';
    	if(array_key_exists('post_type', $_POST)){
	    	$post_type_arr = $_POST['post_type'][0];
	    	$args = array(
	    		'post_per_page' => -1,
	    		'post_type' => 'publish',
				'post_type' => $post_type_arr
			);
			$query = new WP_Query( $args );
			$posts = $query->posts;
			
			foreach($posts as $post) {
				$post_html.= '<h2>'.$post->post_title.'</h2>';
				$post_html.= '<p>'.$post->post_content.'</p>';	
			}
		}
		$response = array( 'success' => true, 'data' => $post_html );
		wp_send_json_success($response);
		wp_die();
    }
    add_action('wp_ajax_post_type_post', 'get_post_by_post' ); 
	add_action('wp_ajax_nopriv_post_type_post', 'get_post_by_post' );

	function set_ajax_url(){ ?>
		<script type="text/javascript">
		    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		</script>
	<?php }
	add_action('wp_head', 'set_ajax_url' ); 
    // used for tracking error messages
	function job_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
	// displays error messages from form submissions
	function job_show_error_success_messages() {
		if($codes = job_errors()->get_error_codes()) {
			echo '<div class="job_errors">';
			    // Loop error codes and display errors
			   foreach($codes as $code){
			        $message = job_errors()->get_error_message($code);
			        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.$message.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			    }
			echo '</div>';
		}	
	}