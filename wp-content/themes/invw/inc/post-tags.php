<?php
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
