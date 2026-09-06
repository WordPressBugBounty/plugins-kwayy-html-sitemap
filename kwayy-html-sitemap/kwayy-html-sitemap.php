<?php
/*
Plugin Name: Kwayy HTML Sitemap
Plugin URI: http://www.kwayyinfotech.com/our-work/kwayy-html-sitemap/
Description: Kwayy HTML Sitemap will generate HTML (not XML) sitemap for your sitemap page. The plugin will not only show Page and Posts but also your other Custom Post Type like Products etc. You can also configure to show or hide your Post Types. You just need to create a page for Sitemap and insert our shortcode <code>[kwayy-sitemap]</code> to display HTML sitemap. You can get support at http://forum.kwayyinfotech.com/
Version: 5.1
Author: Kwayy Infotech
Author URI: http://www.kwayyinfotech.com/
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'kwayyhs_activate' );
function kwayyhs_activate() {
	kwayyhs_set_default_option();
	update_option( 'kwayyhs_welcome_notice_dismissed', '0' );
}

add_action( 'admin_notices', 'kwayyhs_welcome_admin_notice' );
function kwayyhs_welcome_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'kwayyhs_welcome_notice_dismissed', '0' ) === '1' ) {
		return;
	}

	$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( $current_page === 'kwayyhs' ) {
		update_option( 'kwayyhs_welcome_notice_dismissed', '1' );
		return;
	}

	$logo_url     = plugins_url( 'images/plugin-thumbnail-96x96.png', __FILE__ );
	$options_url  = admin_url( 'options-general.php?page=kwayyhs' );
	$notice_nonce = wp_create_nonce( 'kwayyhs_dismiss_notice' );
	?>
	<div class="notice notice-info is-dismissible kwayyhs-welcome-notice" data-nonce="<?php echo esc_attr( $notice_nonce ); ?>" style="border-left-color: #2271b1; padding: 14px 18px; margin: 16px 0; background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
		<div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
			<div style="flex-shrink: 0; display: flex; align-items: center;">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="Kwayy HTML Sitemap" style="width: 90px; height: 90px; object-fit: cover; display: block;" />
			</div>
			<div style="flex: 1; min-width: 240px;">
				<h3 style="margin: 0 0 15px 0; font-size: 25px; font-weight: 600; color: #1d2327; display: flex; align-items: center; gap: 8px;">
					<?php esc_html_e( 'Welcome to Kwayy HTML Sitemap', 'kwayy-html-sitemap' ); ?>
					<span style="background: #2271b1; color: #fff; font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 10px; line-height: 1.3;">v5.1</span>
				</h3>
				<p style="margin: 0; color: #50575e; font-size: 13px; line-height: 1.5;">
					<?php printf( __( 'Thank you for installing Kwayy HTML Sitemap! Your options menu is located under <strong>Settings &rarr; <a href="%s" style="text-decoration: none; color: #2271b1; font-weight: 600;">Kwayy HTML Sitemap</a></strong>. Configure your post types, taxonomies, and sitemap hierarchy anytime.', 'kwayy-html-sitemap' ), esc_url( $options_url ) ); ?>
				</p>
			</div>
			<div style="flex-shrink: 0; display: flex; align-items: center; gap: 8px;">
				<a href="<?php echo esc_url( $options_url ); ?>" class="button button-primary" style="height: 36px; line-height: 34px; padding: 0 16px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; border-radius: 4px;">
					<span class="dashicons dashicons-admin-settings" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
					<?php esc_html_e( 'Go to Kwayy HTML Sitemap Options', 'kwayy-html-sitemap' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'wp_ajax_kwayyhs_dismiss_welcome_notice', 'kwayyhs_dismiss_welcome_notice' );
function kwayyhs_dismiss_welcome_notice() {
	check_ajax_referer( 'kwayyhs_dismiss_notice', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Permission denied.', 'kwayy-html-sitemap' ) ), 403 );
	}

	update_option( 'kwayyhs_welcome_notice_dismissed', '1' );
	wp_send_json_success();
}

function kwayyhs_theme_upgrade_notice() { ?>
	<div id="message" class="updated notice is-dismissible">
		<p><?php esc_html_e( 'Kwayy HTML Sitemap options saved successfully.', 'kwayy-html-sitemap' ); ?></p>
	</div>
<?php
}

function kwayyhs_post_types(){
	$args=array(
	  'public'   => true
	);
	
	$output     = 'objects';
	$operator   = 'and';
	$post_types = get_post_types($args,$output,$operator); 
	
	unset($post_types["attachment"]);
	
	return $post_types;
}

function kwayyhs_taxonomies(){
	$args = array(
		'public' => true,
	);
	$output     = 'objects';
	$operator   = 'and';
	$taxonomies = get_taxonomies( $args, $output, $operator );
	
	unset( $taxonomies['post_format'] );
	
	return $taxonomies;
}

function kwayyhs_get_all_items() {
	$items = array();
	
	// 1. Post Types
	$post_types = kwayyhs_post_types();
	foreach ( $post_types as $pt_name => $pt_obj ) {
		$key = 'cpt_' . $pt_name;
		$icon = 'dashicons-admin-post';
		if ( $pt_name === 'page' ) {
			$icon = 'dashicons-admin-page';
		} elseif ( ! empty( $pt_obj->menu_icon ) && is_string( $pt_obj->menu_icon ) && strpos( $pt_obj->menu_icon, 'dashicons-' ) === 0 ) {
			$icon = $pt_obj->menu_icon;
		}
		
		$items[ $key ] = array(
			'key'   => $key,
			'type'  => 'cpt',
			'slug'  => $pt_name,
			'label' => isset( $pt_obj->labels->name ) ? $pt_obj->labels->name : ucfirst( $pt_name ),
			'icon'  => $icon,
			'object'=> $pt_obj,
		);
	}
	
	// 2. Taxonomies
	$taxonomies = kwayyhs_taxonomies();
	foreach ( $taxonomies as $tax_name => $tax_obj ) {
		$key = 'tax_' . $tax_name;
		$icon = 'dashicons-tag';
		if ( $tax_name === 'category' || strpos( $tax_name, 'cat' ) !== false ) {
			$icon = 'dashicons-category';
		}
		
		$items[ $key ] = array(
			'key'   => $key,
			'type'  => 'taxonomy',
			'slug'  => $tax_name,
			'label' => isset( $tax_obj->labels->name ) ? $tax_obj->labels->name : ucfirst( $tax_name ),
			'icon'  => $icon,
			'object'=> $tax_obj,
		);
	}
	
	return $items;
}

function kwayyhs_get_ordered_items() {
	$all_items = kwayyhs_get_all_items();
	$raw_sortorder = get_option('kwayyhs_sortorder');
	
	$ordered_keys = array();
	if ( ! empty( $raw_sortorder ) ) {
		$raw_array = explode( ',', (string)$raw_sortorder );
		foreach ( $raw_array as $raw_key ) {
			$raw_key = trim( $raw_key );
			if ( empty( $raw_key ) ) continue;
			
			if ( strpos( $raw_key, 'cpt_' ) !== 0 && strpos( $raw_key, 'tax_' ) !== 0 ) {
				if ( isset( $all_items['cpt_' . $raw_key] ) ) {
					$raw_key = 'cpt_' . $raw_key;
				} elseif ( isset( $all_items['tax_' . $raw_key] ) ) {
					$raw_key = 'tax_' . $raw_key;
				}
			}
			
			if ( isset( $all_items[ $raw_key ] ) && ! in_array( $raw_key, $ordered_keys, true ) ) {
				$ordered_keys[] = $raw_key;
			}
		}
	}
	
	if ( empty( $ordered_keys ) ) {
		$cpts = kwayyhs_post_types();
		foreach ( $cpts as $pt_name => $pt_obj ) {
			$cpt_key = 'cpt_' . $pt_name;
			if ( isset( $all_items[ $cpt_key ] ) && ! in_array( $cpt_key, $ordered_keys, true ) ) {
				$ordered_keys[] = $cpt_key;
			}
			
			$pt_taxes = get_object_taxonomies( $pt_name );
			foreach ( $pt_taxes as $tax_name ) {
				$tax_key = 'tax_' . $tax_name;
				if ( isset( $all_items[ $tax_key ] ) && ! in_array( $tax_key, $ordered_keys, true ) ) {
					$ordered_keys[] = $tax_key;
				}
			}
		}
		
		foreach ( array_keys( $all_items ) as $item_key ) {
			if ( ! in_array( $item_key, $ordered_keys, true ) ) {
				$ordered_keys[] = $item_key;
			}
		}
	} else {
		$missing_items = array_diff( array_keys( $all_items ), $ordered_keys );
		foreach ( $missing_items as $missing_key ) {
			if ( strpos( $missing_key, 'tax_' ) === 0 ) {
				$tax_name = substr( $missing_key, 4 );
				$tax_obj = get_taxonomy( $tax_name );
				$inserted = false;
				if ( $tax_obj && ! empty( $tax_obj->object_type ) ) {
					foreach ( $tax_obj->object_type as $associated_cpt ) {
						$cpt_key = 'cpt_' . $associated_cpt;
						$pos = array_search( $cpt_key, $ordered_keys, true );
						if ( $pos !== false ) {
							array_splice( $ordered_keys, $pos + 1, 0, $missing_key );
							$inserted = true;
							break;
						}
					}
				}
				if ( ! $inserted ) {
					$ordered_keys[] = $missing_key;
				}
			} else {
				$ordered_keys[] = $missing_key;
			}
		}
	}
	
	$ordered_items = array();
	foreach ( $ordered_keys as $key ) {
		if ( isset( $all_items[ $key ] ) ) {
			$item = $all_items[ $key ];
			
			$active_opt = get_option( 'kwayyhs_active_' . $key );
			if ( $active_opt === false ) {
				$active_opt = get_option( 'kwayyhs_active_' . $item['slug'], 'active' );
			}
			$item['active'] = ( $active_opt === 'active' );
			
			$newname_opt = get_option( 'kwayyhs_newname_' . $key );
			if ( empty( $newname_opt ) ) {
				$newname_opt = get_option( 'kwayyhs_newname_' . $item['slug'] );
			}
			$item['custom_title'] = ! empty( $newname_opt ) ? $newname_opt : $item['label'];
			
			if ( $item['type'] === 'cpt' ) {
				$item['archive_link'] = ( get_option( 'kwayyhs_archive_' . $key, 'no' ) === 'yes' );
			}
			
			$ordered_items[ $key ] = $item;
		}
	}
	
	return $ordered_items;
}

function kwayyhs_set_default_option(){
	$items = kwayyhs_get_ordered_items();
	$keys  = array_keys( $items );
	
	foreach ( $items as $key => $item ) {
		add_option( 'kwayyhs_active_' . $key, 'active' );
		if ( $item['type'] === 'cpt' ) {
			add_option( 'kwayyhs_active_' . $item['slug'], 'active' );
		}
	}
	add_option( 'kwayyhs_sortorder', implode( ',', $keys ) );
}

add_action( 'admin_enqueue_scripts', 'kwayyhs_admin_scripts' );
add_action( 'admin_menu', 'kwayyhs_adminbar_menu' );
add_action( 'plugin_action_links_' . plugin_basename(__FILE__), 'kwayyhs_plugin_actions');

function kwayyhs_plugin_actions($links){
	$new_links = array();
	$adminlink = admin_url( 'options-general.php?page=kwayyhs' );
	$new_links[] = '<a href="' . esc_url( $adminlink ) . '">' . esc_html__( 'Settings', 'kwayy-html-sitemap' ) . '</a>';
	return array_merge( $links, $new_links );
}

function kwayyhs_adminbar_menu(){
	if ( current_user_can( 'manage_options' ) ) {
		add_options_page(
			__( 'Kwayy HTML Sitemap Options', 'kwayy-html-sitemap' ),
			__( 'Kwayy HTML Sitemap Options', 'kwayy-html-sitemap' ),
			'manage_options',
			'kwayyhs',
			'kwayyhs_page'
		);
	}
}

function kwayyhs_page(){
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'kwayy-html-sitemap' ) );
	}

	update_option( 'kwayyhs_welcome_notice_dismissed', '1' );
	
	// Processing options update
	if ( isset( $_POST['kwayyhs-update'] ) ) {
		check_admin_referer( 'kwayyhs_save_settings', 'kwayyhs_nonce' );

		if ( isset( $_POST['kwayyhs-exclude'] ) ) {
			if ( is_array( $_POST['kwayyhs-exclude'] ) ) {
				$exclude_items = array();
				$raw_excludes  = wp_unslash( $_POST['kwayyhs-exclude'] );
				foreach ( $raw_excludes as $item_val ) {
					$item_val = trim( sanitize_text_field( $item_val ) );
					if ( strpos( $item_val, 'term_' ) === 0 ) {
						$term_id = absint( substr( $item_val, 5 ) );
						if ( $term_id > 0 ) {
							$exclude_items[] = 'term_' . $term_id;
						}
					} else {
						$post_id = absint( $item_val );
						if ( $post_id > 0 ) {
							$exclude_items[] = $post_id;
						}
					}
				}
				$exclude = implode( ',', array_unique( $exclude_items ) );
			} else {
				$raw_exclude = sanitize_text_field( wp_unslash( $_POST['kwayyhs-exclude'] ) );
				$raw_parts   = array_filter( array_map( 'trim', explode( ',', $raw_exclude ) ) );
				$exclude_items = array();
				foreach ( $raw_parts as $p_val ) {
					if ( strpos( $p_val, 'term_' ) === 0 ) {
						$term_id = absint( substr( $p_val, 5 ) );
						if ( $term_id > 0 ) {
							$exclude_items[] = 'term_' . $term_id;
						}
					} else {
						$post_id = absint( $p_val );
						if ( $post_id > 0 ) {
							$exclude_items[] = $post_id;
						}
					}
				}
				$exclude = implode( ',', array_unique( $exclude_items ) );
			}
		} else {
			$exclude = '';
		}

		$all_items = kwayyhs_get_all_items();

		if ( isset( $_POST['kwayyhs-sortorder'] ) ) {
			$raw_sortorder = sanitize_text_field( wp_unslash( $_POST['kwayyhs-sortorder'] ) );
			$sortorder_keys = array_filter( array_map( 'trim', explode( ',', $raw_sortorder ) ) );
			$valid_sortorder = array();
			foreach ( $sortorder_keys as $skey ) {
				if ( isset( $all_items[ $skey ] ) && ! in_array( $skey, $valid_sortorder, true ) ) {
					$valid_sortorder[] = $skey;
				}
			}
			// Append any registered items not included
			foreach ( array_keys( $all_items ) as $item_key ) {
				if ( ! in_array( $item_key, $valid_sortorder, true ) ) {
					$valid_sortorder[] = $item_key;
				}
			}
			update_option( 'kwayyhs_sortorder', implode( ',', $valid_sortorder ) );
		}

		update_option( 'kwayyhs_exclude', $exclude );
		
		foreach ( $all_items as $key => $item ){
			if ( isset( $_POST['kwayyhs_active_' . $key] ) ){
				update_option( 'kwayyhs_active_' . $key, 'active' );
				if ( $item['type'] === 'cpt' ) {
					update_option( 'kwayyhs_active_' . $item['slug'], 'active' );
				}
			} else {
				update_option( 'kwayyhs_active_' . $key, 'deactive' );
				if ( $item['type'] === 'cpt' ) {
					update_option( 'kwayyhs_active_' . $item['slug'], 'deactive' );
				}
			}
			
			if ( isset( $_POST['kwayyhs_newname_' . $key] ) ){
				$new_title = sanitize_text_field( wp_unslash( $_POST['kwayyhs_newname_' . $key] ) );
				update_option( 'kwayyhs_newname_' . $key, $new_title );
				if ( $item['type'] === 'cpt' ) {
					update_option( 'kwayyhs_newname_' . $item['slug'], $new_title );
				}
			}

			if ( $item['type'] === 'cpt' ) {
				if ( isset( $_POST['kwayyhs_archive_' . $key] ) ) {
					update_option( 'kwayyhs_archive_' . $key, 'yes' );
				} else {
					update_option( 'kwayyhs_archive_' . $key, 'no' );
				}
			}
		}

		add_action( 'admin_notices', 'kwayyhs_theme_upgrade_notice' );
	}
	
	$ordered_items     = kwayyhs_get_ordered_items();
	$kwayyhs_sortorder = implode( ',', array_keys( $ordered_items ) );
	$kwayyhs_exclude   = get_option('kwayyhs_exclude');
	$excluded_ids      = array_filter( array_map( 'trim', explode( ',', (string)$kwayyhs_exclude ) ) );

	// Fetch posts for Exclude Select2 dropdown
	$all_posts_by_cpt = array();
	$post_types       = kwayyhs_post_types();
	foreach ( $post_types as $pt ) {
		$cpt_posts = get_posts( array(
			'post_type'        => $pt->name,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => false,
		) );
		
		$icon = 'dashicons-admin-post';
		if ( $pt->name === 'page' ) {
			$icon = 'dashicons-admin-page';
		} elseif ( ! empty( $pt->menu_icon ) && is_string( $pt->menu_icon ) && strpos( $pt->menu_icon, 'dashicons-' ) === 0 ) {
			$icon = $pt->menu_icon;
		}

		$all_posts_by_cpt[ $pt->name ] = array(
			'label' => $pt->labels->name,
			'icon'  => $icon,
			'posts' => $cpt_posts,
		);
	}

	// Fetch taxonomy terms for Exclude Select2 dropdown
	$all_terms_by_tax = array();
	$taxonomies       = kwayyhs_taxonomies();
	foreach ( $taxonomies as $tax ) {
		$tax_terms = get_terms( array(
			'taxonomy'   => $tax->name,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );

		$icon = 'dashicons-tag';
		if ( $tax->name === 'category' || strpos( $tax->name, 'cat' ) !== false ) {
			$icon = 'dashicons-category';
		}

		if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ) {
			$all_terms_by_tax[ $tax->name ] = array(
				'label' => $tax->labels->name,
				'icon'  => $icon,
				'terms' => $tax_terms,
			);
		}
	}
	?>
	
	<div class="wrap kwayyhs-wrap">
		<div class="kwayyhs-header">
			<img src="<?php echo esc_url( plugins_url( 'images/plugin-thumbnail-96x96.png', __FILE__ ) ); ?>" alt="Kwayy HTML Sitemap Logo" class="kwayyhs-header-logo" width="60" height="60" />
			<div class="kwayyhs-header-title">
				<h1>Kwayy HTML Sitemap <span class="kwayyhs-badge">v5.1</span></h1>
				<p class="description"><?php _e( 'Configure sitemap post types, taxonomies, reorder display hierarchy, and exclude specific posts, pages, or terms.', 'kwayy-html-sitemap' ); ?></p>
			</div>
		</div>
		<hr class="wp-header-end">
	
		<form method="post" action="">
			<input type="hidden" name="kwayyhs-update" id="kwayyhs-update" value="y" />
		
			<?php
			wp_nonce_field( 'kwayyhs_save_settings', 'kwayyhs_nonce' );
			?>
    
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						
						<!-- Card 1: Post Types & Taxonomies Configuration -->
						<div class="postbox kwayyhs-card">
							<div class="postbox-header">
								<h2 class="hndle"><span class="dashicons dashicons-category"></span><span class="kwayyhs-hndle-title"><?php _e( 'Sitemap Sections & Order', 'kwayy-html-sitemap' ); ?></span></h2>
							</div>
							<div class="inside">
								<p class="description">
									<?php _e( 'Drag items to reorder post types and taxonomies on the sitemap page. Use the checkboxes to enable or disable sections, or click <strong>Change Title</strong> to customize display titles.', 'kwayy-html-sitemap' ); ?>
								</p>

								<div class="kwayyhs-table-header">
									<div class="kwayyhs-col-drag"><?php _e( 'Order', 'kwayy-html-sitemap' ); ?></div>
									<div class="kwayyhs-col-show"><?php _e( 'Show', 'kwayy-html-sitemap' ); ?></div>
									<div class="kwayyhs-col-type"><?php _e( 'Type', 'kwayy-html-sitemap' ); ?></div>
									<div class="kwayyhs-col-title"><?php _e( 'Display Name', 'kwayy-html-sitemap' ); ?></div>
									<div class="kwayyhs-col-options"><?php _e( 'Options', 'kwayy-html-sitemap' ); ?></div>
									<div class="kwayyhs-col-slug"><?php _e( 'Slug', 'kwayy-html-sitemap' ); ?></div>
								</div>

								<ul id="kwayyhs-sortable" class="kwayyhs-sortable-list">
									<?php echo kwayyhs_sortableList( $ordered_items ); ?>
								</ul>
							</div>
						</div>

						<!-- Card 2: Exclude Items -->
						<div class="postbox kwayyhs-card">
							<div class="postbox-header">
								<h2 class="hndle"><span class="dashicons dashicons-hidden"></span><span class="kwayyhs-hndle-title"><?php _e( 'Exclude Posts, Pages & Terms', 'kwayy-html-sitemap' ); ?></span></h2>
							</div>
							<div class="inside">
								<p class="description">
									<?php _e( 'Select posts, pages, or taxonomy terms to exclude from the sitemap. Only items belonging to currently enabled post types or taxonomies above will be available.', 'kwayy-html-sitemap' ); ?>
								</p>

								<div class="kwayyhs-field-group">
									<label for="kwayyhs-exclude" class="kwayyhs-field-label"><?php _e( 'Select Items to Exclude:', 'kwayy-html-sitemap' ); ?></label>
									<select name="kwayyhs-exclude[]" id="kwayyhs-exclude" class="kwayyhs-select2" multiple="multiple" style="width: 100%;" data-placeholder="<?php _e( 'Search and select posts or terms to exclude...', 'kwayy-html-sitemap' ); ?>">
										<?php foreach ( $all_posts_by_cpt as $pt_slug => $pt_data ) : 
											if ( empty( $pt_data['posts'] ) ) continue;
										?>
											<optgroup label="<?php echo esc_attr( $pt_data['label'] . ' (Post Type)' ); ?>" data-cpt="<?php echo esc_attr( 'cpt_' . $pt_slug ); ?>">
												<?php foreach ( $pt_data['posts'] as $p ) : ?>
													<option value="<?php echo esc_attr( $p->ID ); ?>" data-cpt="<?php echo esc_attr( 'cpt_' . $pt_slug ); ?>" data-icon="<?php echo esc_attr( $pt_data['icon'] ); ?>" <?php selected( in_array( (string)$p->ID, $excluded_ids, true ) ); ?>>
														<?php echo esc_html( $p->post_title ? $p->post_title : '(No title - ID: ' . $p->ID . ')' ); ?> (#<?php echo esc_html( $p->ID ); ?>)
													</option>
												<?php endforeach; ?>
											</optgroup>
										<?php endforeach; ?>

										<?php foreach ( $all_terms_by_tax as $tax_slug => $tax_data ) : 
											if ( empty( $tax_data['terms'] ) ) continue;
										?>
											<optgroup label="<?php echo esc_attr( $tax_data['label'] . ' (Taxonomy Terms)' ); ?>" data-cpt="<?php echo esc_attr( 'tax_' . $tax_slug ); ?>">
												<?php foreach ( $tax_data['terms'] as $t ) : 
													$term_val = 'term_' . $t->term_id;
												?>
													<option value="<?php echo esc_attr( $term_val ); ?>" data-cpt="<?php echo esc_attr( 'tax_' . $tax_slug ); ?>" data-icon="<?php echo esc_attr( $tax_data['icon'] ); ?>" <?php selected( in_array( $term_val, $excluded_ids, true ) ); ?>>
														<?php echo esc_html( $t->name ); ?> (#<?php echo esc_html( $t->term_id ); ?>)
													</option>
												<?php endforeach; ?>
											</optgroup>
										<?php endforeach; ?>
									</select>
									<p class="description"><?php _e( 'Search by title, term name, or ID. You can select multiple items to hide them from the sitemap.', 'kwayy-html-sitemap' ); ?></p>
								</div>
							</div>
						</div>

						<input type="hidden" name="kwayyhs-sortorder" id="kwayyhs-sortorder" value="<?php echo esc_attr( $kwayyhs_sortorder ); ?>" />
						
						<div class="kwayyhs-actions">
							<?php submit_button( __( 'Save Changes', 'kwayy-html-sitemap' ), 'primary large', 'submit', false ); ?>
						</div>

					</div><!-- #post-body-content -->

					<!-- Sidebar -->
					<div id="postbox-container-1" class="postbox-container">
						
						<div class="postbox kwayyhs-card">
							<div class="postbox-header">
								<h2 class="hndle"><span class="dashicons dashicons-book"></span><span class="kwayyhs-hndle-title"><?php _e( 'Quick Guide', 'kwayy-html-sitemap' ); ?></span></h2>
							</div>
							<div class="inside">
								<div class="kwayyhs-shortcode-display">
									<span class="kwayyhs-shortcode-label"><?php _e( 'Shortcode:', 'kwayy-html-sitemap' ); ?></span>
									<code class="kwayyhs-code">[kwayy-sitemap]</code>
								</div>
								<ol class="kwayyhs-steps">
									<li><?php _e( 'Select & order post types and taxonomies on the left.', 'kwayy-html-sitemap' ); ?></li>
									<li><?php _e( 'Create a new page (e.g. "Sitemap").', 'kwayy-html-sitemap' ); ?></li>
									<li><?php _e( 'Insert shortcode <code>[kwayy-sitemap]</code> into the page.', 'kwayy-html-sitemap' ); ?></li>
								</ol>
								<hr class="kwayyhs-divider" />
								<div class="kwayyhs-sidebar-buttons">
									<a href="https://pbminfotech.support/" target="_blank" class="button button-secondary"><span class="dashicons dashicons-editor-help"></span> <?php _e( 'Get Help', 'kwayy-html-sitemap' ); ?></a>
									<a href="https://pbminfotech.com/kwayy-html-sitemap/" target="_blank" class="button button-secondary"><span class="dashicons dashicons-flag"></span> <?php _e( 'Official Site', 'kwayy-html-sitemap' ); ?></a>
								</div>
							</div>
						</div>

						<div class="postbox kwayyhs-card">
							<div class="postbox-header">
								<h2 class="hndle"><span class="dashicons dashicons-heart"></span><span class="kwayyhs-hndle-title"><?php _e( 'Support Us', 'kwayy-html-sitemap' ); ?></span></h2>
							</div>
							<div class="inside" style="text-align:center;">
								<a href="https://pbminfotech.com/?referer=kwayy-banner" target="_blank" rel="noopener noreferrer" class="kwayyhs-ad-link">
									<img src="<?php echo esc_url( plugins_url( 'images/ad-banner-500x500.png', __FILE__ ) ); ?>" alt="PBM Infotech" class="kwayyhs-ad-banner" />
								</a>
							</div>
						</div>

					</div><!-- #post-container-1 -->
				</div><!-- #post-body -->
			</div><!-- #poststuff -->
		</form>
	</div><!-- .wrap -->
	
	<?php
}

function kwayyhs_sortableList( $items ){
	$return = '';
	
	foreach ( $items as $key => $item ) {
		$checked = $item['active'] ? ' checked="checked" ' : '';
		$title   = esc_html( $item['custom_title'] );
		$type    = $item['type'];
		$slug    = esc_html( $item['slug'] );
		$icon    = esc_attr( $item['icon'] );
		
		$type_badge = ( $type === 'cpt' ) 
			? '<span class="kwayyhs-type-icon kwayyhs-badge-cpt" title="' . esc_attr__( 'Custom Post Type', 'kwayy-html-sitemap' ) . '"><span class="dashicons ' . $icon . '"></span></span>'
			: '<span class="kwayyhs-type-icon kwayyhs-badge-tax" title="' . esc_attr__( 'Taxonomy', 'kwayy-html-sitemap' ) . '"><span class="dashicons ' . $icon . '"></span></span>';
			
		$options_col = '';
		if ( $type === 'cpt' ) {
			$archive_checked = ! empty( $item['archive_link'] ) ? ' checked="checked" ' : '';
			$options_col = '<label class="kwayyhs-archive-label" title="' . esc_attr__( 'Include CPT Page/Archive URL as the first link in this section', 'kwayy-html-sitemap' ) . '">
				<input name="kwayyhs_archive_' . esc_attr( $key ) . '" type="checkbox" value="yes" ' . $archive_checked . ' /> ' . __( 'Add CPT Link', 'kwayy-html-sitemap' ) . '
			</label>';
		} else {
			$options_col = '<span class="kwayyhs-no-options">—</span>';
		}
		
		$return .= '
		<li class="kwayyhs-ui-state-default kwayyhs-item-' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '">
			<div class="kwayyhs-cpt kwayyhs-cpt-row">
				<div class="kwayyhs-col-drag kwayyhs-dragable-handler-wrap">
					<span class="kwayyhs-dragable-handler dashicons dashicons-menu" title="' . esc_attr__( 'Drag to reorder', 'kwayy-html-sitemap' ) . '"></span>
				</div>
				<div class="kwayyhs-col-show kwayyhs-dragable-checkbox">
					<label class="kwayyhs-checkbox-label">
						<input name="kwayyhs_active_' . esc_attr( $key ) . '" id="kwayyhs_active_' . esc_attr( $key ) . '" type="checkbox" value="active" ' . $checked . ' />
					</label>
				</div>
				<div class="kwayyhs-col-type">
					' . $type_badge . '
				</div>
				<div class="kwayyhs-col-title kwayyhs-cpt-name">
					<span class="kwayyhs-cpt-name-title">' . $title . '</span>
					&nbsp; <span class="kwayyhs_changename">(<a href="#" title="' . esc_attr__( 'Change title for sitemap display', 'kwayy-html-sitemap' ) . '">' . __( 'Change Title', 'kwayy-html-sitemap' ) . '</a>)</span>
					<div class="kwayyhs-newname">
						<input type="text" name="kwayyhs_newname_' . esc_attr( $key ) . '" value="' . esc_attr( $item['custom_title'] ) . '" class="kwayyhs-newname-input" />
						<a class="kwayy-save-newname button button-small button-primary" href="#">' . __( 'OK', 'kwayy-html-sitemap' ) . '</a>
						<a class="kwayy-cancel-newname button button-small" href="#">' . __( 'Cancel', 'kwayy-html-sitemap' ) . '</a>
					</div>
				</div>
				<div class="kwayyhs-col-options">
					' . $options_col . '
				</div>
				<div class="kwayyhs-col-slug kwayyhs-cpt-slug">
					<span class="kwayyhs-slug-badge"><code>' . $slug . '</code></span>
				</div>
			</div>
		</li>
		';
	}
	return $return;
}

function kwayyhs_admin_scripts( $hook ) {
	wp_enqueue_style( 'dashicons' );

	if ( 'settings_page_kwayyhs' === $hook ) {
		wp_enqueue_style( 'select2-css', plugins_url( 'css/select2.min.css', __FILE__ ), array(), '4.0.13' );
		wp_enqueue_script( 'select2-js', plugins_url( 'js/select2.min.js', __FILE__ ), array( 'jquery' ), '4.0.13', true );

		wp_enqueue_style( 'kwayyhs-custom-css', plugins_url( 'css/kwayy-html-sitemap.css', __FILE__ ), array( 'select2-css' ), '5.1' );
		wp_enqueue_script( 'kwayyhs-custom-js', plugins_url( 'js/kwayy-html-sitemap.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'select2-js' ), '5.1', true );

		wp_localize_script( 'kwayyhs-custom-js', 'kwayyhs_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'kwayyhs_dismiss_notice' ),
		) );
	} elseif ( get_option( 'kwayyhs_welcome_notice_dismissed', '0' ) !== '1' ) {
		wp_enqueue_script( 'kwayyhs-custom-js', plugins_url( 'js/kwayy-html-sitemap.js', __FILE__ ), array( 'jquery' ), '5.1', true );
		wp_localize_script( 'kwayyhs-custom-js', 'kwayyhs_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'kwayyhs_dismiss_notice' ),
		) );
	}
}

/******************* SHORTCODE *********************/
//[kwayy-sitemap]
function shortcode_kwayy_sitemap( $atts ){
	$return        = '<div class="kwayy-html-sitemap-wrapper">';
	$ordered_items = kwayyhs_get_ordered_items();
	
	foreach ( $ordered_items as $key => $item ) {
		if ( ! $item['active'] ) {
			continue;
		}
		
		$title = $item['custom_title'];
		
		if ( $item['type'] === 'cpt' ) {
			$include_archive = ! empty( $item['archive_link'] );
			$return .= kwayyhs_get_post_by_post_type( $item['slug'], $title, 'menu_order', 'ASC', $include_archive );
		} elseif ( $item['type'] === 'taxonomy' ) {
			$return .= kwayyhs_get_taxonomy_terms( $item['slug'], $title );
		}
	}
	
	$return .= '</div> <!-- .kwayy-html-sitemap-wrapper -->';
	return $return;
}

add_shortcode( 'kwayy-sitemap', 'shortcode_kwayy_sitemap' );

function kwayyhs_get_post_by_post_type( $postype , $title , $orderby = 'menu_order' , $order = 'ASC' , $include_archive = false ){
	global $post;
	$curr_page_id = '';
	
	if( isset($post->ID) ){
		$curr_page_id = $post->ID;
	}
	
	$args = array( 'post_type' => $postype, 'posts_per_page' => -1, 'orderby' => $orderby, 'order' => $order );
	$loop = new WP_Query( $args );
	$posts = $loop->posts;
	wp_reset_postdata();

	$subposts = kwayyhs_get_subpost( $posts , 0 , $curr_page_id );
	
	$archive_html = '';
	if ( $include_archive ) {
		$archive_url = '';
		if ( $postype === 'post' ) {
			$page_for_posts = get_option( 'page_for_posts' );
			if ( $page_for_posts ) {
				$archive_url = get_permalink( $page_for_posts );
			} else {
				$archive_url = get_post_type_archive_link( 'post' );
				if ( ! $archive_url ) {
					$archive_url = home_url( '/' );
				}
			}
		} else {
			$archive_url = get_post_type_archive_link( $postype );
		}
		
		if ( $archive_url ) {
			$archive_html = '<li class="kwayyhs-cpt-archive-item"><a href="' . esc_url( $archive_url ) . '"><strong>' . sprintf( esc_html__( 'All %s', 'kwayy-html-sitemap' ), esc_html( $title ) ) . '</strong></a></li>';
		}
	}
	
	if ( empty( $subposts ) && empty( $archive_html ) ) {
		return '';
	}
	
	$return  = '<h2 class="kwayy-html-sitemap-post-title kwayy-'.esc_attr($postype).'-title">'.esc_html($title).'</h2>';
	$return .= '<ul class="kwayy-html-sitemap-post-list kwayy-'.esc_attr($postype).'-list">';
	$return .= $archive_html;
	$return .= $subposts;
	$return .= '</ul>';
	
	return $return;
}

function kwayyhs_get_subpost( $posts , $parent_id , $curr_page_id ){
	$return = '';
	$posts2 = $posts;
	
	$kwayyhs_exclude = get_option('kwayyhs_exclude');
	if ( is_array( $kwayyhs_exclude ) ) {
		$excluded_ids = array_map( 'intval', $kwayyhs_exclude );
	} else {
		$excluded_ids = array_filter( array_map( 'intval', explode( ',', (string)$kwayyhs_exclude ) ) );
	}
	
	if( ! empty( $posts ) && is_array( $posts ) ){
		foreach($posts as $post){
			if($post->post_parent == $parent_id){
				if( $post->ID != $curr_page_id ){
					if( ! in_array( (int)$post->ID, $excluded_ids, true ) ){
						$return .= '<li><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';
						$return .= kwayyhs_get_subpost( $posts2, $post->ID , $curr_page_id );
						$return .= '</li>';
					}
				}
			}
		}
		if( $return != '' && $parent_id != 0 ){
			$return = '<ul>'.$return.'</ul>';
		}
	}
	
	return $return;
}

function kwayyhs_get_taxonomy_terms( $taxonomy_name, $title ) {
	if ( ! taxonomy_exists( $taxonomy_name ) ) {
		return '';
	}
	
	$terms = get_terms( array(
		'taxonomy'   => $taxonomy_name,
		'hide_empty' => true,
		'parent'     => 0,
	) );
	
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	
	$kwayyhs_exclude = get_option('kwayyhs_exclude');
	$excluded_items  = array_filter( array_map( 'trim', explode( ',', (string)$kwayyhs_exclude ) ) );
	
	$term_list = kwayyhs_get_subterms( $terms, $taxonomy_name, $excluded_items );
	if ( empty( trim( $term_list ) ) ) {
		return '';
	}
	
	$return  = '<h2 class="kwayy-html-sitemap-tax-title kwayy-tax-'.esc_attr($taxonomy_name).'-title">'.esc_html($title).'</h2>';
	$return .= '<ul class="kwayy-html-sitemap-tax-list kwayy-tax-'.esc_attr($taxonomy_name).'-list">';
	$return .= $term_list;
	$return .= '</ul>';
	
	return $return;
}

function kwayyhs_get_subterms( $terms, $taxonomy_name, $excluded_items = array() ) {
	$return = '';
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return $return;
	}
	
	foreach ( $terms as $term ) {
		$term_val = 'term_' . $term->term_id;
		if ( in_array( $term_val, $excluded_items, true ) ) {
			continue;
		}
		
		$term_link = get_term_link( $term );
		if ( is_wp_error( $term_link ) ) {
			continue;
		}
		
		$sub = '';
		$child_terms = get_terms( array(
			'taxonomy'   => $taxonomy_name,
			'hide_empty' => true,
			'parent'     => $term->term_id,
		) );
		
		if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) {
			$sub = kwayyhs_get_subterms( $child_terms, $taxonomy_name, $excluded_items );
		}
		
		$return .= '<li><a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a>';
		if ( ! empty( $sub ) ) {
			$return .= '<ul>' . $sub . '</ul>';
		}
		$return .= '</li>';
	}
	
	return $return;
}
