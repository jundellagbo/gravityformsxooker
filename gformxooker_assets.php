<?php

function gformxooker_admin_assets( $hook ) {
  wp_enqueue_style( 'gformxooker_admin_style', plugin_dir_url( __FILE__ ) . 'assets/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'gformxooker_admin_assets' );


function gformxooker_frontend_assets() {
  wp_enqueue_style( 'gformxooker_frontend_style', plugin_dir_url( __FILE__ ) . 'assets/price-grid.css' );
  wp_enqueue_script( 'gformxooker_frontend_script', plugin_dir_url( __FILE__ ) . 'assets/price-grid.js', null, null, true );
}
add_action( 'wp_enqueue_scripts', 'gformxooker_frontend_assets' );