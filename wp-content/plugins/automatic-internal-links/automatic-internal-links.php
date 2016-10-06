<?php

/**
 * Plugin Name: Automatic Internal Links
 * Plugin URI: http://github.com/georgestephanis/automatic-internal-links/
 * Description: Provide automatic callout embeds for internal links.
 * Version: 0.9 
 * Author: George Stephanis
 * Author URI: http://stephanis.info/
 * Text Domain: automatic-internal-links
 */

add_filter( 'the_content', 'gs_automatic_internal_links' );
function gs_automatic_internal_links( $content ) {
	if ( preg_match_all( '/href="(?P<url>(https?)?' . preg_quote( site_url( '', 'relative' ), '/' ) . '[^"]+)".*<\/p>/i', $content, $matches, PREG_SET_ORDER ) ) {
		$find = $replace = array();

		foreach ( $matches as $match ) {
			$find[] = $match[0];
			$replace[] = $match[0] . "\r\n" . gs_internal_link_box( $match['url'] );
		}

		$content = str_replace( $find, $replace, $content );
		wp_enqueue_style( 'automatic-internal-links', plugins_url( 'style.css', __FILE__ ), '', filemtime( dirname( __FILE__ ) . '/style.css' ) );
	//	wp_style_add_data( 'automatic-internal-links', 'rtl', 'replace' ); # todo: make this file.
	}
	return $content;
}

/**
 * A way to get a short excerpt link box for a post.
 *
 * @param $to int|string The ID or URL to the post in question.
 *
 * @return string|null
 */
function gs_internal_link_box( $to ) {
	if ( filter_var( $to, FILTER_VALIDATE_URL) !== FALSE ) {
		// If we are passed in a URL…
		$to = url_to_postid( $to );
	}

	if ( is_numeric( $to ) && $to ) {
		// If we have passed in a Post ID…
		$post = get_post( $to );
	}

	if ( empty( $post ) || is_wp_error( $post ) ) {
		return null;
	}

	ob_start();
	?>
	<div class="internal-link-box__wrap">
		<a class="internal-link-box__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<?php echo get_the_post_thumbnail( $post ); ?>
			<?php endif; ?>
			<h4><?php echo get_the_title( $post ); ?></h4>
			<small><?php echo esc_html( get_the_date( '', $post ) ); ?></small>
			<em><?php esc_html_e( 'Read more &raquo;', 'automatic-internal-links' ); ?></em>
		</a>
	</div>
	<?php
	return apply_filters( 'gs_internal_link_box', ob_get_clean(), $post );
}
