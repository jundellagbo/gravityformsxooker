<?php
/**
 * Plugin Name: Gravity Forms Xooker
 * Plugin URI: https://gravityforms.com
 * Description: Extended functionality for Gravity Form from Xooker Team
 * Version: 1.0.0
 * Author: JJXooker
 * Author URI: mailto:jj@xooker.com
 * License: GPL-2.0+
 * Text Domain: gravityformsstripe
 * Domain Path: /languages
 *
 */

 require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

 require_once plugin_dir_path( __FILE__ ) . 'FSD_Data_Encryption.php';

 require_once  plugin_dir_path( __FILE__ ) . 'gformxooker_functions.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_custom_metaboxes.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_form_settings.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_rest_api.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_stripe_products.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_posttypes.php';