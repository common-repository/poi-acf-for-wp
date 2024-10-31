<?php if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) die( header( 'Location: /') );

// handle added locations that would be useful for a WooCommerce website
class POI_ACF_WordPress extends POI_ACF_Location_Group {
	// method to grab the instance of this singleton
	public static function instance( $options=false ) { return self::_instance( __CLASS__, $options ); }
	protected function __construct() { parent::__construct(); }

	// initialize this group
	protected function initialize() {
		$this->slug = 'pages';
		$this->name = __( 'Pages', 'poi-acf-wp' );

		// finish normal initialization
		parent::initialize();
	}
}

// security
if ( defined( 'ABSPATH' ) && function_exists( 'add_action' ) )
	POI_ACF_WordPress::instance();
	
	
// handle added locations that would be useful for a WooCommerce website
class POI_ACF_WooCommerce_Products extends POI_ACF_Location_Group {
	// method to grab the instance of this singleton
	public static function instance( $options=false ) { return self::_instance( __CLASS__, $options ); }
	protected function __construct() { parent::__construct(); }

	// initialize this group
	protected function initialize() {
		$this->slug = 'products';
		$this->name = __( 'WooCommerce Products', 'poi-acf-wp' );

		// finish normal initialization
		parent::initialize();
	}
}

// security
//if ( defined( 'ABSPATH' ) && function_exists( 'add_action' ) )
//POI_ACF_WooCommerce_Products::instance();	
