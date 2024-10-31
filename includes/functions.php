<?php
	
	global $poi_acf_dir, $poi_acf_url;
	
	if(!function_exists('pre_export')){
		function pre_export($data){
			echo '<pre>';
			var_export($data);
			echo '</pre>';
		}
	}
	
	if(!function_exists('pre')){
		function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}
	}
	
	if(!function_exists('pree')){
		function pree($data){
			echo '<pre>';
			print_r($data);
			echo '</pre>';
	
		}
	}
	
	if(!function_exists('sanitize_poi_acf_data')){
		function sanitize_poi_acf_data( $input ) {
			if(is_array($input)){		
				$new_input = array();	
				foreach ( $input as $key => $val ) {
					$new_input[ $key ] = (is_array($val)?sanitize_poi_acf_data($val):stripslashes(sanitize_text_field( $val )));
				}			
			}else{
				$new_input = stripslashes(sanitize_text_field($input));			
				if(stripos($new_input, '@') && is_email($new_input)){
					$new_input = sanitize_email($new_input);
				}
				if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
					$new_input = esc_url($new_input);
				}			
			}	
			return $new_input;
		}
	}
	
	if(!function_exists('poi_acf_admin_scripts')){
	
	
	
		function poi_acf_admin_scripts() {
	
	
			$pages_array = array('poi_acf-admin-settings');
	
			if((isset($_GET['page']) && in_array($_GET['page'], $pages_array)) ){
	
	
	
	
	
				wp_enqueue_style( 'poi_acf-grid-front', plugins_url('assets/css/bootstrap.min.css', dirname(__FILE__)), array(), date('mhi') );
	
				wp_register_style('poi_acf-admin', plugins_url('assets/css/admin-style.css', dirname(__FILE__)));
	
	
	
	
	
				wp_enqueue_style( 'poi_acf-admin' );
	
				wp_enqueue_script(
	
					'poi_acf-bs-scripts',
	
					plugins_url('assets/js/bootstrap.min.js?t='.time(), dirname(__FILE__)),
	
					array('jquery')
	
				);
	
	
	
	
	
				wp_enqueue_script(
	
					'poi_acf-scripts',
	
					plugins_url('assets/js/admin-script.js?t='.time(), dirname(__FILE__)),
	
					array('jquery')
	
				);
	
	
				$translation_array = array(
	
					'this_url' => admin_url( 'edit.php?post_type=acf-field-group&page=poi_acf-admin-settings' ),
					'poi_acf_tab' => (isset($_GET['t'])?$_GET['t']:'0'),
					'poi_acf_general_nonce' => wp_create_nonce('poi_acf_general_nonce_action'),
					'file_del_success' => __('File deleted successfully.', 'poi-acf-wp'),
					'file_del_confirm' => __('Are you sure? You want to delete author.php file.', 'poi-acf-wp'),
	
				);
	
				wp_localize_script( 'poi_acf-scripts', 'poi_acf_obj', $translation_array );
	
			}
	
		}
	
	}
	
	add_action( 'admin_enqueue_scripts', 'poi_acf_admin_scripts');
	add_action('wp_enqueue_scripts', 'poi_acf_front_scripts');
	add_action('admin_menu', 'poi_acf_admin_menu');
	add_action('wp_ajax_poi_acf_general_settings_update', 'poi_acf_general_settings_update');
	
	function poi_acf_enqueue_field_script($post_id){
	
		$field_objects = get_field_objects($post_id);
	
		if(!empty($field_objects)){
			foreach ($field_objects as $name => $field){
	
				$class_name = 'acf_field_'.$field['type'];
	
				if(class_exists($class_name)){
	
	
					$acf_field = new $class_name();
	
					if(method_exists($acf_field, 'input_admin_enqueue_scripts')){
						$acf_field->input_admin_enqueue_scripts();
					}
	
				}
	
			}
		}
	}
	
	
	if(!function_exists('poi_acf_front_scripts')){
	
	
	
		function poi_acf_front_scripts(){
	
	
			global $post;
	
	
	
	//        poi_acf_enqueue_field_script($post->ID);
	
	
	
	
	
			if(!is_admin() && is_object($post) && $post->post_type == 'poi_acf_template'){
				wp_die(__('Sorry, You are not allowed to access this page.', 'poi-acf-wp') );
			}
	
			if(is_author()){
	
	
	
						wp_enqueue_style( 'poi_acf-bs-front', plugins_url('assets/css/bootstrap.min.css', dirname(__FILE__)), array(), date('mhi') );
	
			}
	
	
			wp_enqueue_style('acf-input');
			wp_enqueue_script('acf-input');
			wp_enqueue_style( 'poi_acf-front-style', plugins_url('assets/css/front-style.css', dirname(__FILE__)), array(), date('mhi') );
			wp_enqueue_script(
	
				'poi_acf-front-scripts',
	
				plugins_url('assets/js/front-script.js?t='.time(), dirname(__FILE__)),
	
				array('jquery')
	
			);
	
	
	
			$translation_array = array(
	
				'ajax_url' => admin_url( 'admin-ajax.php' ),
	
			);
	
	
			wp_localize_script( 'poi_acf-front-scripts', 'poi_acf_obj', $translation_array );
	
		}
	
	}
	
	if(!function_exists('poi_acf_admin_menu')){
	
	
		function poi_acf_admin_menu() {
	
			if(!is_admin()) {
				return;
			}
	
			$slug = 'edit.php?post_type=acf-field-group';
			$cap = acf_get_setting('capability');
	
			$page = add_submenu_page($slug, 'POI ACF', 'POI ACF WordPress', $cap, 'poi_acf-admin-settings', 'poi_acf_admin_settings_callback' );
	
	
		}
	
	
	
	}
	
	if(!function_exists('poi_acf_admin_settings_callback')){
	
		function poi_acf_admin_settings_callback(){
	
	
	
			global $poi_acf_dir;
			require_once($poi_acf_dir.'admin/admin-settings.php');
	
	
	
		}
	
	
	
	}
	
	if(!function_exists('poi_acf_add_new_settings_form_call_back')){
	
		function poi_acf_add_new_settings_form_call_back(){
	
			global  $poi_acf_dir;
	
			ob_start();
	
			include_once($poi_acf_dir.'/admin/general-settings-form.php');
	
			$poi_acf_content = ob_get_contents();
			ob_clean();
	
			echo $poi_acf_content;
	
		}
	
	}
	
	if(!function_exists('poi_acf_general_settings_update')){
		function poi_acf_general_settings_update(){
	
			if(isset($_POST['poi_acf_general_settings'])){
	
				if(!isset($_POST['poi_acf_general_nonce'])
					|| !wp_verify_nonce($_POST['poi_acf_general_nonce'], 'poi_acf_general_nonce_action')){
	
					wp_die(__("Sorry, your nonce did not verified.", 'poi-acf-wp'));
	
	
				}else{
	
	
						$poi_acf_general_settings = sanitize_poi_acf_data($_POST['poi_acf_general_settings']);
						echo update_option('poi_acf_general_settings', $poi_acf_general_settings);
	
				}
	
			}
	
			wp_die();
		}
	}
	
	add_action('acf/save_post', 'poi_acf_save_post');
	
	if(!function_exists('poi_acf_save_post')){
		function poi_acf_save_post($post_id){
	
	
	
			if(function_exists('get_fields')){
	
	
				$api = POI_ACF_API::instance();
	
				$field_groups = $api->get_field_groups( array(
					'pages' => $post_id,
				) );
	
				if(empty($field_groups) || is_admin()){return;}
	
	//            if()
	
	
	
				$user_id = get_current_user_id();
	
				$fields = get_field_objects($post_id);
	
	
				$fields_name_value_array = array_map(function($field){
	
	
	
					$single_field = array();
					$single_field['ID'] = $field['ID'];
					$single_field['key'] = $field['key'];
					$single_field['label'] = $field['label'];
					$single_field['name'] = $field['name'];
					$single_field['value'] = $field['value'];
	
					return $single_field;
	
				}, $fields);
	
				$fields_name_value_array = array_filter($fields_name_value_array, function($key){
	
	
					if(strpos($key, '_') === 0){
						return false;
					}else{
						return true;
					}
	
				}, ARRAY_FILTER_USE_KEY);
	
	
				update_user_meta($user_id, 'poi_acf_field_data-'.$post_id, $fields_name_value_array);
	
			}
	
		}
	}
	
	if(!function_exists('poi_acf_get_user_fields_data')){
		function poi_acf_get_user_fields_data($user_id){
	
			global $wpdb;
	
			$meta_prefix = 'poi_acf_field_data';
	
			$query = "SELECT * FROM $wpdb->usermeta WHERE meta_key LIKE '%$meta_prefix%' AND user_id = $user_id";
	
			$poi_fields_row = $wpdb->get_results($query);
	
			$poi_acf_fields_array = array();
	
			if(!empty($poi_fields_row)){
	
				foreach ($poi_fields_row as $index => $single_row){
	
	
					$meta_key = $single_row->meta_key;
					$meta_key_array = explode('-', $meta_key);
					$page_id = end($meta_key_array);
	
					$page = get_post($page_id);
	
	
					$poi_acf_fields_array[$page->post_title] = maybe_unserialize($single_row->meta_value);
				}
			}
	
			return $poi_acf_fields_array;
	
		}
	}
	
	function poi_acf_is_author_file_exist(){
	
	
		$copy_to_dir = get_stylesheet_directory();
	
	
		return file_exists($copy_to_dir.'/author.php');
	
	}
	
	
	
	function poi_acf_delete_author_file(){
	
		$copy_to_dir = get_stylesheet_directory();
		$file_path = $copy_to_dir.'/author.php';
	
	
		if(file_exists($file_path)){
	
		   return unlink($file_path);
	
		}else{
	
			return false;
	
		}
	}
	
	add_action('wp_ajax_poi_acf_delete_author_file', function(){
	
	   echo poi_acf_delete_author_file();
	
	   wp_die();
	
	});
	
	
	
	add_action('wp_ajax_poi_acf_copy_author_file', 'poi_acf_copy_author_file_callback');
	
	if(!function_exists('poi_acf_copy_author_file_callback')){
	
		function poi_acf_copy_author_file_callback(){
	
			global $poi_acf_dir;
	
	
			$copy_to_dir = get_stylesheet_directory();
			$copy_from_dir = $poi_acf_dir.'templates/';
	
	
			$response_array = array();
			$is_file_exist = 'no';
			$file_copy = 'no';
	
	
			if(file_exists($copy_to_dir.'/author.php')){
				$is_file_exist = 'yes';
			}else{
	
				try{
	
					$copy =	copy($copy_from_dir.'author.php', $copy_to_dir.'/author.php');
					if($copy){
						$file_copy = 'yes';
					}
	
				} catch(Exception $e){
	
					$response_array['is_error'] = 'yes';
					$response_array['error'] = $e->getMessage();
	
				}
	
	
	
			}
	
			$response_array['file_exist'] = $is_file_exist;
	
			if($is_file_exist == 'yes' && isset($_POST['replace_file'])){
	
	
				try{
	
					$copy =	copy($copy_from_dir.'author.php', $copy_to_dir.'/author.php');
	
					if($copy){
						$file_copy = 'yes';
					}
	
				} catch(Exception $e){
	
					$response_array['is_error'] = 'yes';
					$response_array['error'] = $e->getMessage();
	
				}
	
			}
			// echo $copy_from_dir;
			$response_array['file_copy'] = $file_copy;
	
			header('Content-Type: application/json');
			echo json_encode($response_array);
			exit;
		}
	}
	
	
	if(!function_exists('poi_acf_get_user_address')){
	
		function poi_acf_get_user_address($user_id=0, $user_meta=array(), $force=false){
	
			$user_id = ($user_id==0?get_current_user_id():$user_id);
	
	
			if(empty($user_meta)){
				$user_meta_pre = get_user_meta($user_id);
				$user_meta_pre = is_array($user_meta_pre)?$user_meta_pre:array();
	
				$user_meta = array_map( function( $a ){ return $a[0]; },  $user_meta_pre);
			}
	
			//pree($user_meta);
	
			extract($user_meta);
			//pree($wp_user_address);
			if(isset($wp_user_address)){
				$wp_user_address_check = maybe_unserialize($wp_user_address);
				if(isset($wp_user_address_check['address'])){
					$force = (!$force && trim($wp_user_address_check['address'])=='');
				}
			}
	
			if(isset($wp_user_address) && !$force){
				$wp_user_address = maybe_unserialize($wp_user_address);
				$address = $wp_user_address['address'];
	
			}else{
	
				$address = array();
				$addresses = '';
	
				if(isset($billing_address_1) && $billing_address_1)
					$addresses .= $billing_address_1;
	
				if(isset($billing_address_2) && $billing_address_2)
					$addresses .= ' '.$billing_address_2;
	
				if(isset($addresses) && trim($addresses))
					$address[] = $addresses;
	
				if(isset($billing_city) && $billing_city)
					$address[] = $billing_city;
	
				if(isset($billing_state) && $billing_state)
					$address[] = $billing_state;
	
				if(isset($billing_postcode) && $billing_postcode)
					$address[] = $billing_postcode;
	
				if(isset($billing_country) && $billing_country)
					$address[] = $billing_country;
	
	
				$address = implode(', ', $address);
	
				wp_inbox_update_address($user_id, $address);
	
			}
	
			return $address;
	
		}
	
	}
	
	
	add_action('init', 'poi_acf_register_template_type');
	if(!function_exists('poi_acf_register_template_type')){
	
		function poi_acf_register_template_type(){
	
	
	
			$supports = array(
				'title', // post title
				'editor', // post content
			);
	
			$labels = array(
				'name' => _x('Poi ACF Template', 'plural'),
				'singular_name' => _x('Poi ACF Templates', 'singular'),
				'menu_name' => _x('Poi ACF Templates', 'admin menu'),
				'name_admin_bar' => _x('Poi ACF Templates', 'admin bar'),
				'add_new' => _x('Add New', 'add new'),
				'add_new_item' => __('Add New Poi Template', 'poi-acf-wp'),
				'new_item' => __('New Poi Template', 'poi-acf-wp'),
				'edit_item' => __('Edit Poi Template', 'poi-acf-wp'),
				'view_item' => __('View Poi ACF Templates', 'poi-acf-wp'),
				'all_items' => __('All Templates', 'poi-acf-wp'),
				'search_items' => __('Search Poi Template', 'poi-acf-wp'),
				'not_found' => __('No Poi Template found.', 'poi-acf-wp'),
			);
	
	
	
			$args = array(
				'supports' => $supports,
				'labels' => $labels,
				'public' => true,
				'query_var' => true,
				'rewrite' => array('slug' => 'poi_acf_template'),
				'has_archive' => true,
				'show_in_rest' => true,
			);
	
			register_post_type('poi_acf_template', $args);
	
	
		}
	
	}
	
	
	
	if(!function_exists('poi_acf_get_field_groups')){
		function poi_acf_get_field_groups(){
	
			$raw_fields_groups = acf_get_raw_field_groups();
	
			$poi_groups = array_map(function($single_group){
	
	//            pree($single_group);
	
				$group_locations = $single_group['location'] ?? array();
	
				$params_array = array();
	
				if(!empty($group_locations)){
					foreach ($group_locations as $index => $single_location){
	
	//                    pree($single_location);
	
						$params = array_column($single_location, 'param');
	
						$params_array = array_merge($params_array, $params);
	
	
					}
				}
	
				if(in_array('pages', $params_array)){
	
					return $single_group;
				}
	
	
			}, $raw_fields_groups);
	
			$poi_groups = array_filter($poi_groups);
	
			return $poi_groups;
	
		}
	}
	
	add_shortcode('poi_acf_field', 'poi_acf_field_callback');
	
	if(!function_exists('poi_acf_field_callback')){
	
		function poi_acf_field_callback($attr){
	
			ob_start();
	
	
	
			global $poi_author_id, $poi_acf_author_fields;
	
			if(is_null($poi_acf_author_fields)){
				return;
			}
	
			$poi_acf_author_fields = $poi_acf_author_fields ?? array();
	
			if(empty($attr) || !isset($attr['field_id']) || empty($poi_acf_author_fields)){
	
				return;
			}
	
			$field_id = $attr['field_id'];
			$ids_array = explode('|', $field_id);
			$group_id = current($ids_array);
			$field_id = end($ids_array);
	
	
	
	//        $poi_acf_fields_keys = array_keys($poi_acf_author_fields);
	//        $poi_acf_fields_values = array_values($poi_acf_author_fields);
	//
	//        $poi_acf_fields_keys = array_map('strtolower', $poi_acf_fields_keys);
	//        $poi_acf_author_fields = array_combine($poi_acf_fields_keys, $poi_acf_fields_values);
	//        $page_name = trim($attr['page']);
	//        $page_name_lower = strtolower($page_name);
	//
	//        $current_page_fields = array_key_exists($page_name_lower, $poi_acf_author_fields) ? $poi_acf_author_fields[$page_name_lower] : array();
	
	
			$current_field = array();
	
			if(!empty($poi_acf_author_fields)){
				foreach ($poi_acf_author_fields as $page_name => $fields_array){
	
					if(!empty($fields_array)){
	
						foreach ($fields_array as $field_name => $single_field){
	
	
	
							if($field_id == $single_field['ID']){
								$current_field = $single_field;
								break;
							}
	
						}
	
					}
	
					if(!empty($current_field)){
						break;
					}
				}
			}
	
			if(empty($current_field)){
	
				return;
	
			}
	
			echo $current_field['value'];
	
	
			$content = ob_get_contents();
	
			ob_end_clean();
	
			return $content;
	
		}
	}
	
	
	add_filter('poi_author_content', function($content, $author_id){
	
		global $poi_acf_general_settings;
	
		$current_template = isset($poi_acf_general_settings['poi_acf_template_selection']) ? $poi_acf_general_settings['poi_acf_template_selection'] : 'default';
	
		if($current_template == 'default'){
	
	
		}else{
	
	
			$template = get_post($current_template);
	
			if(!empty($template)){
	
				$template_content = $template->post_content;
	
	
				$content =  apply_filters('the_content', $template_content);
	
			}
	
		}
	
	
	
	
		return $content;
	
	}, 10, 2);
	
	
	add_action('admin_head', function (){
	
		?>
	
		<style>
	
			#menu-posts-poi_acf_template{
				display: none;
			}
			.editor-post-preview{
				display: none;
			}
		</style>
	
		<?php
	
	});
	
	
	
	add_action('init', function(){
	
	
	});
	
	function poi_acf_plugin_links($links) { 
		global $poi_acf_premium_link, $poi_acf_pro;

		$settings_link = '<a href="edit.php?post_type=acf-field-group&page=poi_acf-admin-settings">'.__('Settings', 'poi-acf-wp').'</a>';
		
		if($poi_acf_pro){
			array_unshift($links, $settings_link); 
		}else{
			 
			$poi_acf_premium_link = '<a href="'.esc_url($poi_acf_premium_link).'" title="'.__('Go Premium', 'poi-acf-wp').'" target=_blank>'.__('Go Premium', 'poi-acf-wp').'</a>'; 
			array_unshift($links, $settings_link, $poi_acf_premium_link); 
		
		}
		
		
		return $links; 
	}