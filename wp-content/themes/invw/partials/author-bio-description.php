<?php
if ( is_author() || is_page() ) {
// Avatar
if ( largo_has_avatar( $author_obj->user_email ) ) {
	echo '<div class="photo span3">' . get_avatar( $author_obj->ID, 256, '', $author_obj->display_name ) . '</div>';
} elseif ( $author_obj->type == 'guest-author' && get_the_post_thumbnail( $author_obj->ID ) ) {
	$photo = get_the_post_thumbnail( $author_obj->ID, array( 256, 256 ) );
	$photo = str_replace( 'attachment-256x256 wp-post-image', 'avatar avatar-256 photo', $photo );
	echo '<div class="photo span3">' . $photo . '</div>';
}
?>
<div class="details span9">
	<?php
	// Author name
	if ( is_author() ) {
		echo '<h1 class="fn n">' . $author_obj->display_name . '</h1>';
	} else if ( count_user_posts($author_obj->ID) > 0) {
		printf( __( '<h3 class="widgettitle"><span class="fn n"><a class="url" href="%1$s" rel="author" title="See all posts by %1$s">%2$s</a></span></h3>', 'largo' ),
			get_author_posts_url($author_obj->ID),
			esc_attr( $author_obj->display_name )
		);
	} else {
		printf( __( '<h3 class="widgettitle"><span class="fn n">%1$s</span></h3>', 'largo' ),
			esc_attr( $author_obj->display_name )
		);
	}

	if ($job_title)
		echo '<p class="job-title">' . esc_attr($job_title) . '</p>';

	if ( $email = $author_obj->user_email ) { ?>
		<p class="email">
			<a href="mailto:<?php echo esc_attr( $email ); ?>" title="e-mail <?php echo esc_attr( $author_obj->display_name ); ?>"><?php echo esc_attr( $email ); ?></a>
		</p><?php
	}

	if (!empty($topics_covered)) { ?>
		<div class="topics-covered">
			<p>Covers:
				<?php
					foreach ($topics_covered as $topic) { ?>
						<span>
							<a href="<?php echo site_url( get_option('category_base') . '/' . $topic->slug); ?>">
							<?php echo $topic->name; ?></a><?php if ($topic !== end($topics_covered)) { ?>,<?php } ?>
						</span><?php
					} ?>
			</p>
		</div>
	<?php
	}

	if ($phone_number)
		echo '<p class="phone-number"><span class="phone">Phone:</span> ' . esc_attr($phone_number) . '</p>';

	// Social media links ?>
	<ul class="social-links">
		<?php if ( $fb = $author_obj->fb ) { ?>
			<li class="facebook">
				<a href="https://facebook.com/<?php echo largo_fb_url_to_username(esc_url( $fb )); ?>"><i class="icon-facebook"></i></a>
			</li>
		<?php } ?>

		<?php if ( $twitter = $author_obj->twitter ) { ?>
			<li class="twitter">
				<a href="https://twitter.com/<?php echo largo_twitter_url_to_username ( $twitter ); ?>"><i class="icon-twitter"></i></a>
			</li>
		<?php } ?>

		<?php if ( $googleplus = $author_obj->googleplus ) { ?>
			<li class="googleplus">
				<a href="<?php echo esc_url( $googleplus ); ?>" title="<?php echo esc_attr( $author_obj->display_name ); ?> on Google+" rel="me"><i class="icon-gplus"></i></a>
			</li>
		<?php } ?>

		<?php if ( $linkedin = $author_obj->linkedin ) { ?>
			<li class="linkedin">
				<a href="<?php echo esc_url( $linkedin ); ?>" title="<?php echo esc_attr( $author_obj->display_name ); ?> on LinkedIn"><i class="icon-linkedin"></i></a>
			</li>
		<?php } ?>
	</ul><?php

	// Description
	if ( $author_obj->description ) {
		echo '<p class="bio">' . esc_attr( $author_obj->description ) . '</p>';
	}

	if ( !is_author() && count_user_posts($author_obj->ID) > 0 ) {
		printf( __( '<span class="author-posts-link"><a class="url" href="%1$s" rel="author" title="See all posts by %1$s">More by %2$s</a></span>', 'largo' ),
			get_author_posts_url($author_obj->ID),
			esc_attr( $author_obj->first_name )
		);
	}
}
else {

	if ( is_author() ) {
		echo '<h1 class="fn n">' . $author_obj->display_name . '</h1>';
	} else if ( count_user_posts($author_obj->ID) > 0) {
		printf( __( '<h3 class="widgettitle"><span class="fn n"><a class="url" href="%1$s" rel="author" title="See all posts by %1$s">%2$s</a></span></h3>', 'largo' ),
			get_author_posts_url($author_obj->ID),
			esc_attr( $author_obj->display_name )
		);
	} else {
		printf( __( '<h3 class="widgettitle"><span class="fn n">%1$s</span></h3>', 'largo' ),
			esc_attr( $author_obj->display_name )
		);
	}

	// Social media links ?>
	<ul class="social-links">
		<?php if ( $fb = $author_obj->fb ) { ?>
			<li class="facebook">
				<a href="https://facebook.com/<?php echo largo_fb_url_to_username(esc_url( $fb )); ?>"><i class="icon-facebook"></i></a>
			</li>
		<?php } ?>

		<?php if ( $twitter = $author_obj->twitter ) { ?>
			<li class="twitter">
				<a href="https://twitter.com/<?php echo largo_twitter_url_to_username ( $twitter ); ?>"><i class="icon-twitter"></i></a>
			</li>
		<?php } ?>

		<?php if ( $googleplus = $author_obj->googleplus ) { ?>
			<li class="googleplus">
				<a href="<?php echo esc_url( $googleplus ); ?>" title="<?php echo esc_attr( $author_obj->display_name ); ?> on Google+" rel="me"><i class="icon-gplus"></i></a>
			</li>
		<?php } ?>

		<?php if ( $linkedin = $author_obj->linkedin ) { ?>
			<li class="linkedin">
				<a href="<?php echo esc_url( $linkedin ); ?>" title="<?php echo esc_attr( $author_obj->display_name ); ?> on LinkedIn"><i class="icon-linkedin"></i></a>
			</li>
		<?php } ?>
	</ul><?php

	// Description
	if ( $author_obj->description ) {
		echo '<p class="bio">' . esc_attr( $author_obj->description ) . '</p>';
	}

	if ( !is_author() && count_user_posts($author_obj->ID) > 0 ) {
		printf( __( '<span class="author-posts-link"><a class="url" href="%1$s" rel="author" title="See all posts by %1$s">More by %2$s</a></span>', 'largo' ),
			get_author_posts_url($author_obj->ID),
			esc_attr( $author_obj->first_name )
		);
	}
}
?>

