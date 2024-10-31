<?php if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) die( header( 'Location: /') );

// handles all extra my account fields, on the edit shipping address screen
class POI_ACF_WP_Pages extends POI_ACF_Location {
	// method to grab the instance of this singleton
	public static function instance( $options=false ) { return self::_instance( __CLASS__, $options ); }
	protected function __construct() { parent::__construct(); }

	// initialize this location
	protected function initialize() {
		$this->slug = 'general_pages';
		$this->name = __( 'All Pages', 'poi-acf-wp' );
		$this->group_slug = 'pages';
		$this->priority = 30;

		// finish normali initialization
		parent::initialize();

		// add our fields to the WC my account page
		// REFERENCE: woocommerce/templates/myaccount/form-edit-address.php @ 45
//		if(isset($_GET['id']))
		add_filter( 'the_content', array( &$this, 'add_fields' ), 100 );

		// handle the saving of the address
		// REFERENCE: woocommerce/includes/class-wc-form-handler.php @ 131
		add_action( 'init', array( &$this, 'save_form' ), 100, 2 );
		
		//add_action( 'save_post', array( &$this, 'poi_updating_acf_group_fields') );
	}
	
	public function poi_updating_acf_group_fields( $post_id ) {
	
		// If this is just a revision, don't send the email.
		if ( 
				is_admin() 
			&& 
				(isset($_POST['post_type']) && $_POST['post_type']=='acf')
			&&
				(isset($_POST['post_ID']) && $_POST['post_ID']==$post_id)
			&&
				(isset($_POST['ID']) && $_POST['ID']==$post_id)				
	
		){
			$get_rule = get_post_meta($post_id, 'rule', true);
			
			if($this->_is_valid_slug($get_rule['value'])){
				update_post_meta($get_rule['value'], 'poi_page_rule', $post_id);
			}
				
		}else{
			return;
		}
		

	}
	
	// register this location with a specific location group
	public function register_with_group( $group ) {
		// register the extra edit shipping fields location
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'title',
			'order'            => 'ASC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => array('page'),
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts_array = get_posts( $args );			
		
		if(!empty($posts_array)){
			foreach($posts_array as $post){
				$group->register_location( array(
					'slug' => $post->ID,
					'name' => $post->post_title,
					'object' => &$this,
				) );
			}
		}
	}

	// determine when this group needs to load the acf_form_head function. should be overriden by child class for it's logic to run
	protected function _needs_form_head() {
		return is_account_page();
	}

	// on the my account page, load our my account assets
	protected function _enqueue_assets() {
		// reused vars
		$launcher = POI_ACF_WC_Launcher::instance();
		$uri = $launcher->plugin_url() . 'assets/';
		$version = $launcher->version();
	}
	
	private function _is_valid_slug($slug){
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => array($slug),
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'any',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => array('publish', 'draft'),
			'suppress_filters' => true 
		);
		$posts_array = get_posts( $args );	
		//pree($posts_array); 	
		// if the object is not a product_cat, then bail
		return !empty($posts_array);
			
			
	}

	// add the fields we need to a new form at the bottom of the my account page
	public function add_fields($content) {
		global $post, $poi_acf_general_settings;
		$api = POI_ACF_API::instance();

		$poi_location = isset($poi_acf_general_settings['poi_acf_display_location']) ? $poi_acf_general_settings['poi_acf_display_location'] : 'after_content';

		//$slug_sub = get_post_meta($post->ID, 'poi_page_rule', true);
		
		
		
		// fetch the list of groups that belong on the top of the my account page
	
		$field_groups = $api->get_field_groups( array(
			'pages' => $post->ID,
		) );


		if(empty($field_groups)){
		    return $content;
        }

		if(!empty($field_groups) && !is_user_logged_in()){
		    wp_die(__('Sorry, you are not allowed to access this page.', 'poi-acf-wp'));
        }


		if($poi_location == 'after_content'){
		    echo $content;
        }
		
		
		$product_id = (isset($_GET['id']) && ($_GET['id']>0) && $this->_is_valid_slug($_GET['id'])?$_GET['id']:$post->ID);
		
		//pree($product_id);exit;
		// if there are no field groups to show, then bail
		if (!$this->_is_valid_slug($post->ID) || ! is_array( $field_groups ) || empty( $field_groups ) )
		return;

		
		// get the group keys from the array of fields
		$group_keys = wp_list_pluck( $field_groups, 'ID' );


		$api = POI_ACF_API::instance();
		// start styling the fields for a woocommerce form
		$api->wc_fields_start();

		// get the information about the current user
		$user = wp_get_current_user();

		$form_id = 'acf_' . $this->slug . '_form';
		// otherwise render the groups
		$this->acf_form( apply_filters( 'poi-acf-' . $this->slug .  '-acf_form-params', array(
			// name the form in the DOM
			'id' => $form_id,
			// assign a new unique id for where to associate the field groups. myaccount_[$user_id]
			'post_id' => $product_id,
			// draw the wc-my-account-top groups
			'field_groups' => $group_keys,
			// do not wrap the groups in a form, because we are already inside a form
			'form' => true,
			// kill the message, because the form already has one
			'updated_message' => true,
			// kill the button. we dont need it
			'submit_value' => __('Save Changes'),
		), 'myaccount_' . $user->ID, $user ) );

		// add the javascript we need in order to make this work via ajax
		$api->acf_js_form_register( '#' . $form_id );

		// stop styling the fields for a woocommerce form
		$api->wc_fields_stop();

        if($poi_location == 'before_content'){
            echo $content;
        }
		

	}

	// handle the address save at the appropriate time
	public function save_form() {

	
		$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
		$acf_post_id = isset($_POST['_acf_post_id']) ? $_POST['_acf_post_id'] : 0;

		$post_id  = sanitize_poi_acf_data($post_id == 0 ? $acf_post_id : $post_id);
		// otherwise, save this shiz
		if($post_id>0)
		    $this->_process_save( $post_id );


	}

	// handle the submitted fields
	protected function _process_save( $post_id=null ) {
//		$post_id = $user_id;
		
		// get the current user if any
//		$user = ! $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id );

        $user = wp_get_current_user();
		
		// if the acf fields validate, then save them
		if ($this->_form_submitted() ) {
			//$post_id = 'user_' . $user->ID;
			// if the function exists (because acf pro is active), then set the form data
			
			
			
			if ( function_exists( 'acf_set_form_data' ) )
			acf_set_form_data( array( 'post_id' => $post_id ) );



			// allow acf to save the fields to the order itself
			do_action( 'acf/save_post', $post_id );
			
			

			// then tell the world of our success
			do_action( 'lou/acf/save_post/type='. $this->slug, $post_id, $user );
		}
	}
}

// security
if ( defined( 'ABSPATH' ) && function_exists( 'add_action' ) )
	POI_ACF_WP_Pages::instance();
