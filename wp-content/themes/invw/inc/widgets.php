<?php

function invw_widgets() {
	$register = array(
		'series_awards_widget' => '/inc/widgets/series-awards.php',
	);

	foreach ( $register as $key => $val ) {
		require_once( get_stylesheet_directory() . $val );
		register_widget( $key );
	}
}
add_action( 'widgets_init', 'invw_widgets', 11 );