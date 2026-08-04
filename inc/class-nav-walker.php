<?php
/**
 * Custom nav walker: adds the animated caret + aria attributes on menu items
 * that have a submenu (used for the "À propos" dropdown). Everything else
 * (sub-menu wrapping, current-menu-item classes...) stays default WordPress.
 *
 * @package SpringCard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SpringCard_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   wp_nav_menu() args.
	 * @param int      $id     Menu item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent = $depth ? str_repeat( "\t", $depth ) : '';

		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[]    = 'menu-item-' . $item->ID;
		$has_children = in_array( 'menu-item-has-children', $classes, true );

		$class_names = implode( ' ', array_filter( $classes ) );
		$output     .= $indent . '<li class="' . esc_attr( $class_names ) . '">';

		$atts           = array();
		$atts['href']   = ! empty( $item->url ) ? $item->url : '#';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

		if ( $has_children ) {
			$atts['aria-haspopup']         = 'true';
			$atts['aria-expanded']         = 'false';
			$atts['data-dropdown-trigger'] = 'true';
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' === $value ) {
				continue;
			}
			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$output .= '<a' . $attributes . '>';
		$output .= esc_html( $title );
		if ( $has_children ) {
			$output .= ' <span class="caret" aria-hidden="true">&#9662;</span>';
		}
		$output .= '</a>';
	}
}
