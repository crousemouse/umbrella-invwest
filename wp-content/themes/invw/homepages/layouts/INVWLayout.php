<?php

include_once get_template_directory() . '/homepages/homepage-class.php';
include_once get_stylesheet_directory() . '/homepages/zones/invw-zones.php';

class INVWLayout extends Homepage {
	function __construct($options=array()) {
		$defaults = array(
			'name' => __('INVW homepage', 'invw'),
			'type' => 'invw',
			'description' => __('Homepage layout for Investigate West', 'invw'),
			'template' => get_stylesheet_directory() . '/homepages/templates/invw_template.php',
			'assets' => array(
#				// If this page had Js, it would be enqueueued like so
#				array(
#					'your_homepage_javascript',
#					get_stylesheet_directory_uri() . '/homepages/assets/js/invw_homepage.js',
#					array()
#				),
				array('invw', get_stylesheet_directory_uri() . '/homepages/assets/css/invw_homepage.css', array(''))
			),
			'prominenceTerms' => array(
				array(
					'name' => __('Top Story', 'largo'),
					'description' => __('Add this label to a post to make it the top story on the homepage', 'largo'),
					'slug' => 'top-story'
				)
			)
		);
		$options = array_merge($defaults, $options);

		$this->init($options);
		$this->load($options);
	}

	/**
	 * Defining the zones
	 *
	 * Zone function names here are turned into variables that are able to be echo()d in the template. Please define the actual zone output generation in homepages/zones/.
	 */

# 	function latestNews() {
# 		return zone_latestNews();
# 	}

	function heroImage() {
		return zone_heroImage();
	}

	function homepagegrid() {
		return zone_homepagegrid();
	}

	function awardwinningseries() {
		return zone_awardwinningseries();
	}

	function journalismwithimpact() {
		return zone_journalismwithimpact();
	}
}


function invw_add_widget_areas() {
	$sidebars = array(
		array(
			'name' => 'Homepage Impact Image',
			'id' => 'homepageimpactimage',
			'description' => __('The image that appears on the homepage between the sections "Award-Winning Series" and "Journalism With Impact".', 'invw'),
			'before_widget' => '<div class="span12 impactimage">',
			'after_widget' => '</div>',
			'before_title' => '',
			'after_title' => '',
		),
		array(
			'name' => 'Series Archive Sidebar',
			'id' => 'seriesarchive',
			'description' => __('A sidebar to be shown on just the archive page for a given series. Associate this with series one at a time in Posts > Series.', 'invw'),
			'before_widget' => '<div class="span12 seriesarchive">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		),
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( $sidebar );
	}
}
add_action( 'widgets_init', 'invw_add_widget_areas' );
