<?php /**
 * Copies from largo_byline() in inc/post-tags.php
 * Modified to output Investigate West Partners for Archives / Sidebars
 *
 * This function was copied into theme-wenews. Changes here should also be made there.
 *
 * @link https://github.com/INN/Largo/issues/415
 * @since Largo 0.5.4
 */
	function largo_byline( $echo = true, $exclude_date = false, $post = null ) {
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
