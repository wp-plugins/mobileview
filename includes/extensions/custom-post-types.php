<?php
add_filter( 'mobileview_extensions_admin', 'mobileview_custom_posts_setup_admin' );
add_filter( 'mobileview_default_settings', 'mobileview_custom_posts_default_settings' );
add_filter( 'pre_get_posts', 'mobileview_custom_posts_pre_get_posts' );
add_filter( 'mobileview_should_show_taxonomy', 'mobileview_custom_post_should_show' );
add_filter( 'mobileview_has_custom_taxonomy', 'mobileview_custom_post_should_show' );
add_filter( 'mobileview_get_custom_taxonomy', 'mobileview_custom_posts_get_taxonomy' );
function mobileview_custom_posts_get_list( $remove_defaults = true ) {
	$default_post_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
	// Get the internal list
	$post_types = get_post_types();	
	if ( $remove_defaults ) {
		return apply_filters( 'mobileview_custom_posts_list', array_diff( $post_types, $default_post_types ) );
	} else {
		return apply_filters( 'mobileview_custom_posts_list', $post_types );
	}
}
function mobileview_custom_posts_get_name_for_post_type( $post_type ) {
	return 'mobileview_show_custom_post_type_' . $post_type;
}
function mobileview_custom_posts_default_settings( $defaults ) {
	$defaults->mobileview_enable_custom_post_types = false;
	$defaults->mobileview_show_custom_post_taxonomy = false;
	$defaults->mobileview_show_custom_post_taxonomy_on_blog = false;
	$post_types = mobileview_custom_posts_get_list( true );
	
	if ( $post_types && count( $post_types )  ) {
		foreach( $post_types as $post_type ) {
			$setting_name = mobileview_custom_posts_get_name_for_post_type( $post_type );
			$defaults->$setting_name = false;
		}
	}	
	return $defaults;
}
function mobileview_custom_posts_setup_admin() {
	$new_admin_info = array();
	$new_admin_info[] = array( 'section-start', 'custom_post_types', __( 'Custom Post Types', 'mobileviewlang' ) );
	// Should be changed to only public types later one, but for testing all included
	$post_types = mobileview_custom_posts_get_list( true );
	if ( $post_types && count( $post_types )  ) {
		$new_admin_info[] = array( 'checkbox', 'mobileview_enable_custom_post_types', __( 'Enable custom post type support', 'mobileviewlang' ), '' );
		$new_admin_info[] = array( 'checkbox', 'mobileview_show_custom_post_taxonomy', __( 'Show custom post type taxonomy on single post page', 'mobileviewlang' ), '' );
		$new_admin_info[] = array( 'checkbox', 'mobileview_show_custom_post_taxonomy_on_blog', __( 'Show custom post type taxonomy on main index pages', 'mobileviewlang' ), '' );
		foreach( $post_types as $post_type ) {
			$new_admin_info[] = array( 'checkbox', mobileview_custom_posts_get_name_for_post_type( $post_type ), sprintf( __( 'Enable \'%s\' post type', 'mobileviewlang' ), $post_type ), '' );
		}
	} else {
		$new_admin_info[] = array( 'copytext', 'no_custom_post_types', __( 'No custom post types found', 'mobileviewlang' ), '', '' );
	}
	$new_admin_info[] = array( 'copytext', 'custom_post_warning', '* ' . __( 'Custom posts often rely on custom meta data and may not always be shown correctly in MobileView', 'mobileviewlang' ), '', '' );	
	$new_admin_info[] = array( 'section-end' );
	return $new_admin_info;
}
function mobileview_custom_posts_pre_get_posts( $query ) {
	// Only modify the custom post type information when a mobile theme is showing
	$settings = mobileview_get_settings();
	if ( !$settings->mobileview_enable_custom_post_types ) {
		return $query;
	}
	if ( is_attachment() ) {
		return $query;
	}
	// Right now only support custom post types on the home page and single post pages
	if ( ( is_single() && !is_page() ) || is_home() ) {	
		// Only employ this logic for when the mobile theme is showing
		if ( mobileview_is_mobile_theme_showing() ) {
			$settings = mobileview_get_settings();
			$post_types = mobileview_custom_posts_get_list( true );
			if ( $post_types && count( $post_types )  ) {			
				$post_type_array = array();
				foreach( $post_types as $post_type ) {
					$setting_name = mobileview_custom_posts_get_name_for_post_type( $post_type );
					if ( isset( $settings->$setting_name ) && $settings->$setting_name ) {
						$post_type_array[] = $post_type;
					}
				}
			}			
			if ( count( $post_type_array ) ) {	
				// Determine the original post type in the query
				$original_post_type = false;			
				if ( isset( $query->queried_object ) ) {
					$original_post_type = $query->queried_object->post_type;
				} else if ( isset( $query->query_vars['post_type'] ) ) {
					$original_post_type = $query->query_vars['post_type'];
				}
				if ( $original_post_type ) {
					$page_for_posts = get_option( 'page_for_posts' );
					if ( isset( $query->queried_object_id ) && ( $query->queried_object_id == $page_for_posts ) ) {
						// we're on the posts page
						$custom_post_types = apply_filters( 'mobileview_custom_posts_pre_get', array_merge( array( 'post' ), $post_type_array ) );
					} else {
						if ( !is_array( $original_post_type ) ) {
							$original_post_type = array( $original_post_type );
						}				
						$custom_post_types = apply_filters( 'mobileview_custom_posts_pre_get', array_merge( $original_post_type, $post_type_array ) );
					}	
					$query->set( 'post_type', $custom_post_types );		
				} else {
					// We're on the home page or possibly another page for a normal site
					$custom_post_types = apply_filters( 'mobileview_custom_posts_pre_get', array_merge( array( 'post' ), $post_type_array ) );
					$query->set( 'post_type', $custom_post_types );	
				}
			}
		}
	}
	return $query;
}
function mobileview_custom_post_should_show( $current ) {
	global $post;
	$settings = mobileview_get_settings();
	$available_posts = mobileview_custom_posts_get_list();
	if ( array_key_exists( $post->post_type, $available_posts ) ) {
		// Show custom post types
		if ( $settings->mobileview_show_custom_post_taxonomy && is_single() && !is_page() ) {
			$current = true;
		}
		if ( $settings->mobileview_show_custom_post_taxonomy_on_blog && !is_single() && !is_page() ) {
			$current = true;
		}
	}
	
	return $current;
}
function mobileview_custom_post_type_get_taxonomies( $post_type ) {
	$post_info = get_object_taxonomies( $post_type );
	return $post_info;
}
function mobileview_custom_posts_get_taxonomy( $tax_info = false ) {
	global $post;
	$taxonomies = mobileview_custom_post_type_get_taxonomies( $post->post_type );
	if ( $taxonomies && count( $taxonomies ) ) {
		foreach( $taxonomies as $taxonomy ) {
			$product_terms = wp_get_object_terms( $post->ID, $taxonomy );
			if ( $product_terms ) {
				$tax_object = get_taxonomy( $taxonomy );
				$tax_info[ $tax_object->labels->name ] = array();		
				foreach( $product_terms as $term ) {	
					$term_info = new stdClass;
					$term_info->name = $term->name;
					$term_info->link = get_term_link( $term->slug, $taxonomy );
					$tax_info[ $tax_object->labels->name ][] = $term_info;
				}
			}
		}
	}
	return $tax_info;
}