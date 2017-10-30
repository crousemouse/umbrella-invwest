<?php 
/**
 * Creates the top area of the invw homepage: The hero image, the top story, the seven smaller stories in the grid.
 *
 * - Hero image
 * - Featured post headline and link and top term and byline
 */
function zone_heroImage() {
	// We don't add this to global $shown_ids because we need this same post later.

	$bigStoryPost = largo_home_single_top();
	
	$img = get_the_post_thumbnail($bigStoryPost->ID, 'medium_large');
	
	$permalink = get_the_permalink($bigStoryPost->ID);
	
	$hero_output = '<a href="'. $permalink .'">'. $img .'</a>';
	
	return $hero_output;

}

/**
 * Creates the homepage grid
 *
 * The layout is like this, with F being the featured post and 1-7 being the other
 * stories on the homepage
 *
 * +-----------+
 * |F |1 |2    |
 * |  |  +-----+
 * |  |  |ME   |
 * +-----------+
 * |R |5    |7 |
 * |  +-----+  |
 * |  |6    |  |
 * +--------+--+
 *
 */
function zone_homepagegrid() {
	global $shown_ids;

	ob_start();

	// Display the first featured post.
	$bigStoryPost = largo_home_single_top();
	$shown_ids[] = $bigStoryPost->ID; // Don't repeat the current post

	?>
		<article class="hg-cell hg-featured">
			<div class="hg-cell-inner">
				<h5 class="top-tag"><?php _e('Top Story'); ?></h5>
				<h2><a href="<?php echo get_permalink($bigStoryPost->ID); ?>"><?php echo $bigStoryPost->post_title; ?></a></h2>
	<?php 
	// Get Custom Taxonomy Partners
	//	Show Investigate West Partner(s)
	$partners_terms_feat = wp_get_object_terms( $bigStoryPost->ID,  'partners' );
	if ( ! empty( $partners_terms_feat ) ) {
	    if ( ! is_wp_error( $partners_terms_feat ) ) {
			echo '<div class="featured home-partner"><span>With</span>';
			foreach( $partners_terms_feat as $term_feat ) {
				echo '<a href="' . get_term_link( $term_feat->slug, 'partners' ) . '" class="home-partner-link">' . esc_html( $term_feat->name ) . '</a>'; 
			}
			echo '</div>';
	     }
     }
	 ?>
			</div>
		</article>

	<?php

	// Display the other posts on the homepage

	/**
	 * Define the ids of the "redacted" and "members exclusive" taxonomies, and query for the most-recent post in them.
	 */
	$redacted_id = 4541; // "Redacted" is a series
	$members_id = 4514; // Members Exclusive series is known as "Sidebar" in the taxonomy

	$redacted_post_query = largo_get_series_posts( $redacted_id, 1 );
	$redacted_post = $redacted_post_query->posts[0];
	$redacted_post->testing = 'redacted';
	$shown_ids[] = $redacted_post->ID;
	
	$members_posts = new WP_Query(array(
		'tax_query' => array(
			array(
				'taxonomy' => 'series',
				'terms'    => $members_id
				),
		),
		'posts_per_page' => 1,
		'post__not_in'=> $shown_ids,
	));
	$members_post = $members_posts->posts[0];
	$members_post->testing = 'member';
	$shown_ids[] = $members_post->ID;
	
	$args = array(
		'paged' => $paged,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field'	   => 'slug',
				'terms'    => 'homepage-featured'
				),
		),
		'posts_per_page' => 5,
		'post__not_in' => $shown_ids,
		'ignore_sticky_posts' => true
	);
	$otherposts_query = new WP_Query($args);
	$otherposts = $otherposts_query->posts;
	
	
	/**
	 * Insert the $members_post and $redacted_post into the posts
	 */
	$grid_posts = array_slice($otherposts, 0, 2, true) +
	array('member' => $members_post) +
	array('redacted' => $redacted_post) +
	array_slice($otherposts, 2, 3, true);
	
	$post_count = 0;

	foreach ($grid_posts as $gp) {
		$gp_id = $gp->ID;
		$post_count++;
		$shown_ids[] = $gp_id;
		?>
			<article class="hg-cell hg-<?php echo $post_count; echo ' '.$gp->testing ;?>">
				<div class="hg-cell-inner">
					<h5 class="top-tag">
						<?php largo_top_term(array('post' => $gp_id));?>
						 <span class="hg-date-published">&nbsp;/&nbsp;<?php echo get_the_date( 'm.d.y', $gp_id ); ?></span>
					</h5>
					<h2><a href="<?php echo get_permalink($gp_id); ?>"><?php echo get_the_title($gp_id); ?></a></h2>
	<?php 
	// Get Custom Taxonomy Partners
	//	Show Investigate West Partner(s)
	$partners_terms = wp_get_object_terms( $gp_id,  'partners' );
	if ( ! empty( $partners_terms ) ) {
	    if ( ! is_wp_error( $partners_terms ) ) {
	    	echo '<div class="featured home-partner"><span>With</span>';
			foreach( $partners_terms as $term ) {
				echo '<a href="' . get_term_link( $term->slug, 'partners' ) . '" class="home-partner-link">' . esc_html( $term->name ) . '</a>'; 
			}
			echo '</div>';
	    }
     }
	 ?>

				</div>
			</article>
		<?php
	}

	return ob_get_clean();
}

/**
 * Given the name of a menu whose menu items consist solely of individual series, returns
 * a list of details about each series. The details included for each series is:
 *
 * series_term - the term object itself.
 * series_url - the URL for the series.
 * series_name - display name of the series.
 * series_top_tag - appears like the top tag in the top grid, but is set manually in the menu
 *   via the Title Attribute field.
 * series_description - the description of the series.
 * series_image_urls - the URL(s) for the image(s) of the award this series received.
 */
function _get_series_from_menu( $menu_name ) {
	$series_menu_items = wp_get_nav_menu_items( $menu_name );

	$series_meta_objects = array();
	foreach ( (array) $series_menu_items as $key => $menu_item ) {
		$series_term = get_term_by( 'id', (int) $menu_item->object_id, 'series' );
		$series_url = get_term_link( $series_term, $series_term->taxonomy );
		$series_name = $series_term->name;
		$series_top_tag = $menu_item->post_excerpt;
		$series_description = $series_term->description;
		/**
		 * Since we're using the menu only for containing extra metadata about the set of series, and
		 * the information in that menu is not used anywhere else, we're re-purposing the Description
		 * field to contain the URL(s) to the award image(s).
		 */
		$series_image_urls = explode( " ", trim( $menu_item->post_content ) );

		$series_meta_objects[] = array(
			'series_term'			=> $series_term,
			'series_url'			=> $series_url,
			'series_name'			=> $series_name,
			'series_top_tag'		=> $series_top_tag,
			'series_description'	=> $series_description,
			'series_image_urls'		=> $series_image_urls,
		);
	}

	return $series_meta_objects;
}

function zone_awardwinningseries() {
	ob_start();

	/**
	 * The series that should appear in the Award-Winning Series section of the Homepage should be
	 * arranged in display order in a menu called "Award-Winning Series".
	 */
	$series_meta_objects = _get_series_from_menu( "Award-Winning Series" );

	$series_count = 0;
	foreach ( $series_meta_objects as $series_meta_object ) {
		$series_count++;
		?>
			<div class="span3 awardwinningseries-cell awardwinningseries-<?php echo $series_count; ?>">
				<div class="awardwinningseries-cell-inner">
					<a href="<?php echo $series_meta_object['series_url']; ?>">
						<img src="<?php echo $series_meta_object['series_image_urls'][0]; ?>">
					</a>
					<h5 class="top-tag"><?php echo $series_meta_object['series_top_tag']; ?></h5>
					<h3><a href="<?php echo $series_meta_object['series_url']; ?>"><?php echo $series_meta_object['series_name']; ?></a></h3>
					<div class="description"><?php echo $series_meta_object['series_description']; ?></div>
				</div>
			</div>
		<?php
		if( $series_count == 4 ) {
			break;
		}
	}

	return ob_get_clean();
}

function zone_journalismwithimpact() {
	ob_start();

	/**
	 * The series that should appear in the "Journalism With Impact" section of the Homepage should be
	 * arranged in display order in a menu called "Journalism With Impact".
	 */
	$series_meta_objects = _get_series_from_menu( "Journalism With Impact" );
	$series_count = 0;
	foreach ( $series_meta_objects as $series_meta_object ) {
		$series_count++;
		?>
			<div class="span3 journalismwithimpact-cell journalismwithimpact-<?php echo $series_count; ?>">
				<div class="journalismwithimpact-cell-inner">
					<a href="<?php echo $series_meta_object['series_url']; ?>">
						<img src="<?php echo $series_meta_object['series_image_urls'][0]; ?>">
					</a>
					<h5 class="top-tag"><?php echo $series_meta_object['series_top_tag']; ?></h5>
					<h3><a href="<?php echo $series_meta_object['series_url']; ?>"><?php echo $series_meta_object['series_name']; ?></a></h3>
					<div class="description"><?php echo $series_meta_object['series_description']; ?></div>
				</div>
			</div>
		<?php
		if( $series_count == 4 ) {
			break;
		}
	}

	return ob_get_clean();
}
