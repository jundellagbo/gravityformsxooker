<?php
/**
 * Plugin Name: Gravity Forms Xooker
 * Plugin URI: https://github.com/jundellagbo/gravityformsxooker
 * Description: Extended functionality for Gravity Form from Xooker Team
 * Version: 1.5.2
 * Author: JJXooker
 * Author URI: mailto:jj@xooker.com
 * License: GPL-2.0+
 * Text Domain: gravityformsxooker
 * Domain Path: /languages
 *
 */

 require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

 require_once  plugin_dir_path( __FILE__ ) . 'gformxooker_functions.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_custom_metaboxes.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_form_settings.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_rest_api.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_posttypes.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_posthandler.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_assets.php';

 require_once plugin_dir_path( __FILE__ ) . 'gformxooker_pricegrid.php';


 use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
  'https://github.com/jundellagbo/gravityformsxooker',
  __FILE__,
  'gravityformsxooker'
);