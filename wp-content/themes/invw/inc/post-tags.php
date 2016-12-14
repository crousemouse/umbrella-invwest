<?php
/**
 * Copies from largo_byline() in inc/post-tags.php
 * Modified to output Investigate West Partners for Archives / Sidebars
 *
 * This function was copied into theme-wenews. Changes here should also be made there.
 *
 * @link https://github.com/INN/Largo/issues/415
 * @since Largo 0.5.4
 */
	function invw_largo_byline( $echo = true, $exclude_date = false, $post = null ) {
		if (!empty($post)) {
			if (is_object($post)) {
				$post_id = $post->ID;
			} else if (is_numeric($post)) {
				$post_id = $post;
			}
		} else {
			$post_id = get_the_ID();
		}

		$values = get_post_custom( $post_id );

		$authors = '';
		if ( function_exists( 'get_coauthors' ) && !isset( $values['largo_byline_text'] ) ) {
			$coauthors = get_coauthors( $post_id );
			foreach( $coauthors as $author ) {
				$byline_text = $author->display_name;
				if ( $org = $author->organization )
					$byline_text .= ' (' . $org . ')';
				$out[] = '<a class="url fn n" href="' . get_author_posts_url( $author->ID, $author->user_nicename ) . '" title="' . esc_attr( sprintf( __( 'Read All Posts By %s', 'largo' ), $author->display_name ) ) . '" rel="author">' . esc_html( $byline_text ) . '</a>';
			}
			if ( count($out) > 1 ) {
				end($out);
				$key = key($out);
				reset($out);
				$authors = implode( ', ', array_slice( $out, 0, -1 ) );
				$authors .= ' <span class="and">' . __( 'and', 'largo' ) . '</span> ' . $out[$key];
			} else {
				$authors = $out[0];
			}
		} else {
			$authors = largo_author_link( false, $post_id );
		}

		$output = sprintf( __('<span class="by-author"><span class="by">By:</span> <span class="author vcard" itemprop="author">%1$s</span></span><span class="sep"> | </span><time class="entry-date updated dtstamp pubdate" datetime="%2$s">%3$s</time>', 'largo'),
			$authors,
			esc_attr( get_the_date( 'c' ) ),
			largo_time( false )
		);

		if ( current_user_can( 'edit_post', $post_id ) )
			$output .=  sprintf( __('<span class="sep"> | </span><span class="edit-link"><a href="%1$s">Edit This Post</a></span>', 'largo'), get_edit_post_link($post_id) );

	 	if ( is_single() && of_get_option( 'clean_read' ) === 'byline' )
	 		$output .=	__('<a href="#" class="clean-read">View as "Clean Read"</a>');
	 		
	 	if ( is_archive() ) {
	 	    $partners_terms_feat = wp_get_object_terms( $post_id,  'partners' );
		       if ( ! empty( $partners_terms_feat ) ) {
	           if ( ! is_wp_error( $partners_terms_feat ) ) {
			      $partners .= '<div><span>With</span> ';
			      foreach( $partners_terms_feat as $term_feat ) {
				     if($counter > 0 ) {
				        $partners .= ', <a href="' . get_term_link( $term_feat->slug, 'partners' ) . '"> ' . esc_html( $term_feat->name ) . '</a>'; 
				     }
				    else {
				      $partners .= '<a href="' . get_term_link( $term_feat->slug, 'partners' ) . '"> ' . esc_html( $term_feat->name ) . '</a>'; 
				   }
				$counter++;
			}
			$partners .= '</div>';
	     }
     }
	 		$output .= $partners;
	 	}

		if ( $echo )
			echo $output;
		return $output;
	}

/**
 * Maybe output the partner for a byline
 *
 * @param int $post_id the ID of the post
 */
function invw_byline_partners( $post_id ) {
	if ( is_archive() ) {
		$partners_terms_feat = wp_get_object_terms( $post_id,  'partners' );

		if ( ! empty( $partners_terms_feat ) ) {
			if ( ! is_wp_error( $partners_terms_feat ) ) {
				$partners .= '<div><span>With</span> ';
				foreach( $partners_terms_feat as $term_feat ) {
					if($counter > 0 ) {
						$partners .= ', <a href="' . get_term_link( $term_feat->slug, 'partners' ) . '"> ' . esc_html( $term_feat->name ) . '</a>'; 
					} else {
						$partners .= '<a href="' . get_term_link( $term_feat->slug, 'partners' ) . '"> ' . esc_html( $term_feat->name ) . '</a>'; 
					}
					$counter++;
				}
				$partners .= '</div>';
			}
		}

		echo $partners;
	}
}

/**
 * INVW custom byline class to add partners on bylines in post archives
 */
include_once( get_template_directory() . '/inc/byline_class.php' );
class INVW_CoAuthors_Byline extends Largo_CoAuthors_Byline {
	/**
	 * Temporary variable used to contain the coauthor being rendered by the loop inside generate_byline();
	 * @see $this->generate_byline();
	 */
	protected $author;

	/**
	 * Temporary variable used for the author ID;
	 * This must be public, because Largo_CoAuthors_Byline's methods incorporate methods from Largo_Byline, and parent classes cannot see private or protected members of extending classes.
	 * @see $this->generate_byline();
	 */
	public $author_id;

	/**
	 * Differs from Largo_CoAuthors_Byline in following ways:
	 *
	 * - adds invw_byline_partners to the list of 
	 *
	 */
	function generate_byline() {
		// get the coauthors for this post
		$coauthors = get_coauthors( $this->post_id );
		$out = array();
		// loop over them
		foreach( $coauthors as $author ) {
			$this->author_id = $author->ID;
			$this->author = $author;

			ob_start();

			$this->avatar();
			$this->author_link();
			$this->job_title();
			$this->organization();
			$this->twitter();

			$byline_temp = ob_get_clean();

			// array of byline html strings
			$out[] = $byline_temp;
		}

		// If there are multiple coauthors, join them with commas and 'and'
		if ( count( $out ) > 1 ) {
			end( $out );
			$key = key( $out );
			reset( $out );
			$authors = implode( ', ', array_slice( $out, 0, -1 ) );
			$authors .= ' <span class="and">' . __( 'and', 'largo' ) . '</span> ' . $out[$key];
		} else {
			$authors = $out[0];
		}


		// Now assemble the One True Byline
		ob_start();
		echo '<span class="by-author"><span class="by">' . __( 'By', 'largo' ) . '</span> <span class="author vcard" itemprop="author">' . $authors . '</span></span>';
		$this->maybe_published_date();

		/*
		 * Significant addition!
		 */
		invw_byline_partners( $this->post_id );

		$this->edit_link();

		$this->output = ob_get_clean();
	}

}

/**
 * Replacement largo_byline that displays the post partner in bylines on archive listings
 * Updated from Largo 0.5.4-style largo_byline to 0.5.5.1-style wrapper for an extended Largo_CoAuthors_Byline in 0.5.5.1 in December 2016
 *
 * This function was copied into theme-wenews. Changes here should also be made there.
 *
 * @see INVW_CoAuthors_Byline
 * @link https://github.com/INN/Largo/issues/415
 * @since Largo 0.5.5
 */
	function largo_byline( $echo = true, $exclude_date = false, $post = null ) {

		// Get the post ID
		if (!empty($post)) {
			if (is_object($post))
				$post_id = $post->ID;
			else if (is_numeric($post))
				$post_id = $post;
		} else {
			$post_id = get_the_ID();
		}

		// Set us up the options
		// This is an array of things to allow us to easily add options in the future
		$options = array(
			'post_id' => $post_id,
			'values' => get_post_custom( $post_id ),
			'exclude_date' => $exclude_date,
		);

		if ( isset( $options['values']['largo_byline_text'] ) && !empty( $options['values']['largo_byline_text'] ) ) {
			// Temporary placeholder for largo custom byline option
			$byline = new Largo_Custom_Byline( $options );
			var_log( "POOP");
		} else if ( function_exists( 'get_coauthors' ) ) {
			// If Co-Authors Plus is enabled and there is not a custom byline
			$byline = new INVW_CoAuthors_Byline( $options );
			var_log( "THIS");
		} else {
			// no custom byline, no coauthors: let's do the default
			$byline = new Largo_Byline( $options );
			var_log( "NORMAL");
		}

		/**
		 * Filter the largo_byline output text to allow adding items at the beginning or the end of the text.
		 *
		 * @since 0.5.4
		 * @param string $partial The HTML of the output of largo_byline(), before the edit link is added.
		 * @link https://github.com/INN/Largo/issues/1070
		 */
		$byline = apply_filters( 'largo_byline', $byline );

		if ( $echo ) {
			echo $byline;
		}
		return $byline;
	}
