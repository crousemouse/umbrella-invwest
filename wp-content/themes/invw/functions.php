<?php
/**
 * Custom functions for Investigate West
 */
 
/**
 * Includes
 */
$includes = array(
	'/inc/widgets.php',		// register widgets
	'/inc/taxonomies.php', // add custom taxonomies
	'/inc/post-tags.php',	// custom largo_byline(), ____ 
	'/inc/banner-hero.php'	// promote the top banner to be a hero.
);
// Perform load
foreach ( $includes as $include ) {
	require_once( get_stylesheet_directory() . $include );
}
 
/**
 * Register a custom homepage layout
 *
 * @see "homepages/layouts/invw_layout.php"
 */
function invw_register_custom_homepage_layout() {
	// load the layout
	include_once __DIR__ . '/homepages/layouts/INVWLayout.php';
	register_homepage_layout('INVWLayout');
}
add_action('init', 'invw_register_custom_homepage_layout', 0);

/**
 * Include fonts from Typekit
 */
function invw_typekit() { ?>
	<script src="//use.typekit.net/hxc6whi.js"></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>
<?php }
add_action( 'wp_head', 'invw_typekit' );

/**
 * Include compiled style.css
 */

function invw_stylesheet() {
	$suffix = (LARGO_DEBUG)? '' : '.min';
	wp_enqueue_style('invw', get_stylesheet_directory_uri().'/css/style' . $suffix . '.css');

	if (is_home()) {
		$suffix = (LARGO_DEBUG) ? '' : '.min';
		wp_enqueue_style('invw-homepage', get_stylesheet_directory_uri().'/homepages/assets/css/invw_homepage' . $suffix . '.css');
	}
}
add_action( 'wp_enqueue_scripts', 'invw_stylesheet', 20 );

/**
 * Add profile fields to user pages.
 *
 * @since 1.0
 */
function invw_add_user_profile_fields($context, $slug, $name) {
	if ($slug == 'partials/author-bio' && $name == 'description') {
		$user = $context['author_obj'];

		$context = array_merge($context, array(
			'job_title' => get_user_meta($user->ID, 'job_title', true)
		));
	}
	return $context;
}
add_filter('largo_render_template_context', 'invw_add_user_profile_fields', 10, 3);


/**
 * Custom Function to output Investigate West Partners in the action largo_after_post_header
 */
function invw_partners() {
global $post;
	$partners_terms_feat = wp_get_object_terms( $post->ID,  'partners' );
	if ( ! empty( $partners_terms_feat ) ) {
	    if ( ! is_wp_error( $partners_terms_feat ) ) {
			$partners .= '<div class="entry-content">';
			$partners .= '<div class="byline-partner"><span>With</span> ';
			foreach( $partners_terms_feat as $term_feat ) {
				if($counter > 0 ) {
				$partners .= ', <a href="' . get_term_link( $term_feat->slug, 'partners' ) . '" class="byline-partner-link"> ' . esc_html( $term_feat->name ) . '</a>'; 
				}
				else {
				$partners .= '<a href="' . get_term_link( $term_feat->slug, 'partners' ) . '" class="byline-partner-link"> ' . esc_html( $term_feat->name ) . '</a>'; 
				}
				$counter++;
			}
			$partners .= '</div>';
			$partners .= '</div>';
			echo $partners;
	     }
     }
}

add_action('largo_after_post_header', 'invw_partners');