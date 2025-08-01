<?php
/**
 * Plugin Name: CB Blog Options
 * Plugin URI: https://github.com/ChillibyteUK/cbp-blog-options
 * Description: A WordPress plugin to manage blog functionality including disabling blog, comments, and gravatars.
 * Version: 1.0.0
 * Author: Chillibyte - DS
 * License: GPL v2 or later
 *
 * @package CB_Blog_Options
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
if ( ! defined( 'CB_BLOG_OPTIONS_VERSION' ) ) {
    define( 'CB_BLOG_OPTIONS_VERSION', '1.0.0' );
}
if ( ! defined( 'CB_BLOG_OPTIONS_PLUGIN_DIR' ) ) {
    define( 'CB_BLOG_OPTIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'CB_BLOG_OPTIONS_PLUGIN_URL' ) ) {
    define( 'CB_BLOG_OPTIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! class_exists( 'CBBlogOptions' ) ) {
	/**
	 * Class CBBlogOptions
	 *
	 * Handles the CB Blog Options plugin functionality including disabling blog, comments, and gravatars.
	 *
	 * @package CB_Blog_Options
	 */
	class CBBlogOptions {

		/**
		 * Define the option name for storing plugin settings.
		 *
		 * @var string Option name for storing plugin settings.
		 */
		private $option_name = 'cb_blog_options';

		/**
		 * Activate the plugin.
		 *
		 * Activates the plugin and sets default options when the plugin is activated.
		 *
		 * Plugin activation callback.
		 *
		 * @return void
		 */
		public static function activate() {
			// Set default options.
			$default_options = array(
				'disable_blog'      => 0,
				'disable_comments'  => 1,
				'disable_gravatars' => 1,
				'disable_tags'      => 0,
			);
			add_option( 'cb_blog_options', $default_options );
		}

		/**
		 * Plugin deactivation callback.
		 *
		 * @return void
		 */
		public static function deactivate() {
			// Optional: Clean up options on deactivation.
			// delete_option('cb_blog_options');.
		}

		/**
		 * CBBlogOptions constructor.
		 *
		 * Initializes the plugin by hooking into WordPress actions.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Initialize plugin hooks and apply blog restrictions.
		 */
		public function init() {
			// Add admin menu.
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

			// Initialize settings.
			add_action( 'admin_init', array( $this, 'settings_init' ) );

			// Apply functionality based on settings.
			$this->apply_blog_restrictions();
		}

		/**
		 * Add admin menu under Tools
		 */
		public function add_admin_menu() {
			add_management_page(
				'CB Blog Options',
				'CB Blog Options',
				'manage_options',
				'cb-blog-options',
				array( $this, 'options_page' )
			);
		}

		/**
		 * Initialize settings
		 */
		public function settings_init() {
			register_setting( 'cb_blog_options', $this->option_name );

			add_settings_section(
				'cb_blog_options_section',
				'Blog Control Options',
				array( $this, 'settings_section_callback' ),
				'cb_blog_options'
			);

			add_settings_field(
				'disable_blog',
				'Disable Blog',
				array( $this, 'disable_blog_render' ),
				'cb_blog_options',
				'cb_blog_options_section'
			);

			add_settings_field(
				'disable_comments',
				'Disable Comments',
				array( $this, 'disable_comments_render' ),
				'cb_blog_options',
				'cb_blog_options_section'
			);

			add_settings_field(
				'disable_gravatars',
				'Disable Gravatars',
				array( $this, 'disable_gravatars_render' ),
				'cb_blog_options',
				'cb_blog_options_section'
			);

			add_settings_field(
				'disable_tags',
				'Disable Tags',
				array( $this, 'disable_tags_render' ),
				'cb_blog_options',
				'cb_blog_options_section'
			);
		}

		/**
		 * Settings section callback
		 */
		public function settings_section_callback() {
			echo '<p>Configure blog functionality options below:</p>';
		}

		/**
		 * Render disable blog checkbox
		 */
		public function disable_blog_render() {
			$options = get_option( $this->option_name );
			$checked = isset( $options['disable_blog'] ) ? $options['disable_blog'] : 0;
			?>
			<input type="checkbox" id="disable_blog" name="<?php echo esc_attr( $this->option_name ); ?>[disable_blog]" value="1" <?php checked( 1, $checked ); ?>>
			<label for="disable_blog">Disable all blog functionality (this will also disable comments and gravatars)</label>
			<?php
		}

		/**
		 * Render disable comments checkbox
		 */
		public function disable_comments_render() {
			$options = get_option( $this->option_name );
			$checked = isset( $options['disable_comments'] ) ? $options['disable_comments'] : 0;
			?>
			<input type="checkbox" id="disable_comments" name="<?php echo esc_attr( $this->option_name ); ?>[disable_comments]" value="1" <?php checked( 1, $checked ); ?>>
			<label for="disable_comments">Disable comments functionality</label>
			<?php
		}

		/**
		 * Render disable gravatars checkbox
		 */
		public function disable_gravatars_render() {
			$options = get_option( $this->option_name );
			$checked = isset( $options['disable_gravatars'] ) ? $options['disable_gravatars'] : 0;
			?>
			<input type="checkbox" id="disable_gravatars" name="<?php echo esc_attr( $this->option_name ); ?>[disable_gravatars]" value="1" <?php checked( 1, $checked ); ?>>
			<label for="disable_gravatars">Disable Gravatars</label>
			<?php
		}

		/**
		 * Render disable tags checkbox
		 */
		public function disable_tags_render() {
			$options = get_option( $this->option_name );
			$checked = isset( $options['disable_tags'] ) ? $options['disable_tags'] : 0;
			?>
			<input type="checkbox" id="disable_tags" name="<?php echo esc_attr( $this->option_name ); ?>[disable_tags]" value="1" <?php checked( 1, $checked ); ?>>
			<label for="disable_tags">Disable Tags</label>
			<?php
		}

		/**
		 * Options page HTML
		 */
		public function options_page() {
			?>
			<div class="wrap">
				<h1>CB Blog Options</h1>
				<form action="options.php" method="post">
					<?php
					settings_fields( 'cb_blog_options' );
					do_settings_sections( 'cb_blog_options' );
					submit_button();
					?>
				</form>
			</div>
			
			<script>
			jQuery(document).ready(function($) {
				// Handle disable blog checkbox logic
				$('#disable_blog').change(function() {
					if ($(this).is(':checked')) {
						$('#disable_comments').prop('checked', true);
						$('#disable_gravatars').prop('checked', true);
						$('#disable_tags').prop('checked', true);
					}
				});
				
				// Prevent unchecking comments/gravatars/tags if blog is disabled
				$('#disable_comments, #disable_gravatars, #disable_tags').change(function() {
					if (!$(this).is(':checked') && $('#disable_blog').is(':checked')) {
						$(this).prop('checked', true);
						alert('Comments, Gravatars, and Tags cannot be enabled while blog is disabled.');
					}
				});
			});
			</script>
			<?php
		}

		/**
		 * Apply blog restrictions based on settings
		 */
		public function apply_blog_restrictions() {
			$options = get_option( $this->option_name );

			// Always remove unwanted dashboard widgets.
			add_action( 'wp_dashboard_setup', array( $this, 'remove_unwanted_dashboard_widgets' ) );

			// Check if blog is disabled.
			if ( isset( $options['disable_blog'] ) && $options['disable_blog'] ) {
				$this->disable_blog_functionality();
			} else {
				// Check individual options.
				if ( isset( $options['disable_comments'] ) && $options['disable_comments'] ) {
					$this->disable_comments_functionality();
				}

				if ( isset( $options['disable_gravatars'] ) && $options['disable_gravatars'] ) {
					$this->disable_gravatars_functionality();
				}

				if ( isset( $options['disable_tags'] ) && $options['disable_tags'] ) {
					$this->disable_tags_functionality();
				}
			}
		}

		/**
		 * Remove unwanted dashboard widgets
		 */
		public function remove_unwanted_dashboard_widgets() {
			// Remove "At a Glance" widget.
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );

			// Remove "WordPress Events and News" widget.
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );

			// Remove "Quick Draft" widget.
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

			// Remove "Activity" widget.
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );

			// Remove Yoast SEO widgets.
			remove_meta_box( 'yoast_db_widget', 'dashboard', 'normal' );
			remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
			remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'normal' );

			// Remove additional Yoast widgets that might exist.
			remove_meta_box( 'yoast_seo_posts_overview', 'dashboard', 'normal' );
			remove_meta_box( 'yoast_seo_posts_overview', 'dashboard', 'side' );
			remove_meta_box( 'wpseo_dashboard_widget', 'dashboard', 'normal' );
		}

		/**
		 * Disable all blog functionality
		 */
		private function disable_blog_functionality() {
			// Hide Posts menu from admin.
			add_action( 'admin_menu', array( $this, 'remove_posts_menu' ), 999 );

			// Disable post type support.
			add_action( 'init', array( $this, 'disable_post_type' ) );

			// Redirect post-related pages.
			add_action( 'admin_init', array( $this, 'redirect_post_pages' ) );

			// Remove post-related dashboard widgets.
			add_action( 'wp_dashboard_setup', array( $this, 'remove_post_dashboard_widgets' ) );

			// Disable comments and gravatars as well.
			$this->disable_comments_functionality();
			$this->disable_gravatars_functionality();
			$this->disable_tags_functionality();

			// Remove blog-related admin bar items.
			add_action( 'admin_bar_menu', array( $this, 'remove_blog_admin_bar_items' ), 999 );

			// Hide blog-related quick draft widget.
			add_action( 'wp_dashboard_setup', array( $this, 'remove_quick_draft_widget' ) );
		}

		/**
		 * Remove Posts menu from admin
		 */
		public function remove_posts_menu() {
			remove_menu_page( 'edit.php' );
			remove_submenu_page( 'edit.php', 'post-new.php' );
		}

		/**
		 * Disable post type
		 */
		public function disable_post_type() {
			global $wp_post_types;
			if ( isset( $wp_post_types['post'] ) ) {
				$wp_post_types['post']->public            = false;
				$wp_post_types['post']->show_ui           = false;
				$wp_post_types['post']->show_in_menu      = false;
				$wp_post_types['post']->show_in_admin_bar = false;
				$wp_post_types['post']->show_in_nav_menus = false;
			}
		}

		/**
		 * Redirect post-related admin pages
		 */
		public function redirect_post_pages() {
			global $pagenow;

			$post_pages = array( 'edit.php', 'post-new.php', 'post.php' );

			if ( in_array( $pagenow, $post_pages, true ) && ( ! isset( $_GET['post_type'] ) || 'post' === $_GET['post_type'] ) ) {
				wp_safe_redirect( admin_url() );
				exit;
			}
		}

		/**
		 * Remove post-related dashboard widgets
		 */
		public function remove_post_dashboard_widgets() {
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		}

		/**
		 * Remove blog-related admin bar items
		 *
		 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
		 */
		public function remove_blog_admin_bar_items( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'new-post' );
		}

		/**
		 * Remove quick draft widget
		 */
		public function remove_quick_draft_widget() {
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		}

		/**
		 * Disable comments functionality
		 */
		private function disable_comments_functionality() {
			// Disable comments support for all post types.
			add_action( 'init', array( $this, 'disable_comments_post_types_support' ) );

			// Close comments on existing posts.
			add_filter( 'comments_open', '__return_false', 20, 2 );
			add_filter( 'pings_open', '__return_false', 20, 2 );

			// Hide existing comments.
			add_filter( 'comments_array', '__return_empty_array', 10, 2 );

			// Remove comments page from admin menu.
			add_action( 'admin_menu', array( $this, 'remove_comments_menu' ) );

			// Redirect comment admin pages.
			add_action( 'admin_init', array( $this, 'redirect_comment_pages' ) );

			// Remove comments from admin bar.
			add_action( 'admin_bar_menu', array( $this, 'remove_comments_admin_bar' ), 999 );

			// Remove comment-related dashboard widgets.
			add_action( 'wp_dashboard_setup', array( $this, 'remove_comment_dashboard_widgets' ) );

			// Hide discussion settings.
			add_action( 'admin_init', array( $this, 'hide_discussion_settings' ) );

			// Hide discussion menu from Settings.
			add_action( 'admin_menu', array( $this, 'remove_discussion_menu' ) );
		}

		/**
		 * Disable comments support for post types
		 */
		public function disable_comments_post_types_support() {
			$post_types = get_post_types();
			foreach ( $post_types as $post_type ) {
				if ( post_type_supports( $post_type, 'comments' ) ) {
					remove_post_type_support( $post_type, 'comments' );
					remove_post_type_support( $post_type, 'trackbacks' );
				}
			}
		}

		/**
		 * Remove comments menu
		 */
		public function remove_comments_menu() {
			remove_menu_page( 'edit-comments.php' );
		}

		/**
		 * Remove discussion menu from Settings
		 */
		public function remove_discussion_menu() {
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}

		/**
		 * Redirect comment pages
		 */
		public function redirect_comment_pages() {
			global $pagenow;

			if ( 'edit-comments.php' === $pagenow ) {
				wp_safe_redirect( admin_url() );
				exit;
			}
		}

		/**
		 * Remove comments from admin bar
		 *
		 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
		 */
		public function remove_comments_admin_bar( $wp_admin_bar ) {
			$wp_admin_bar->remove_node( 'comments' );
		}

		/**
		 * Remove comment dashboard widgets
		 */
		public function remove_comment_dashboard_widgets() {
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		}

		/**
		 * Hide discussion settings
		 */
		public function hide_discussion_settings() {
			add_action( 'admin_head', array( $this, 'hide_discussion_settings_css' ) );
		}

		/**
		 * CSS to hide discussion settings
		 */
		public function hide_discussion_settings_css() {
			global $pagenow;

			if ( 'options-discussion.php' === $pagenow ) {
				echo '<style>
					body { display: none; }
				</style>';
				echo '<script>
					window.location.href = "' . esc_url( admin_url() ) . '";
				</script>';
			}
		}

		/**
		 * Disable Gravatars functionality
		 */
		private function disable_gravatars_functionality() {
			// Disable gravatars.
			add_filter( 'pre_option_show_avatars', '__return_zero' );

			// Remove avatar from user profile.
			add_filter( 'user_profile_picture_description', '__return_empty_string' );

			// Replace avatar with blank image or remove entirely.
			add_filter( 'get_avatar', array( $this, 'disable_gravatar' ), 10, 5 );
		}

		/**
		 * Replace avatar with empty string
		 *
		 * @param string $avatar      The avatar HTML.
		 * @param mixed  $id_or_email The user ID or email.
		 * @param int    $size        The avatar size.
		 * @param string $default_avatar     The default avatar URL.
		 * @param string $alt         The alt text.
		 * @return string Empty string to disable avatars.
		 */
		public function disable_gravatar( $avatar, $id_or_email, $size, $default_avatar, $alt ) {
			return '';
		}

		/**
		 * Disable Tags functionality
		 */
		private function disable_tags_functionality() {
			// Remove tags support from posts.
			add_action( 'init', array( $this, 'unregister_tags' ) );

			// Remove tags submenu from Posts menu.
			add_action( 'admin_menu', array( $this, 'remove_tags_menu' ) );

			// Remove tags metabox from post editor - multiple hooks.
			add_action( 'add_meta_boxes', array( $this, 'remove_tags_metabox' ), 999 );
			add_action( 'admin_init', array( $this, 'remove_tags_metabox' ) );
			add_action( 'admin_head', array( $this, 'remove_tags_metabox' ) );
			add_action( 'load-post.php', array( $this, 'remove_tags_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'remove_tags_metabox' ) );

			// Also try to remove post tag support entirely.
			add_action( 'init', array( $this, 'remove_post_tag_support' ), 999 );

			// Hide with CSS as last resort.
			add_action( 'admin_head', array( $this, 'hide_tags_with_css' ) );

			// Completely unregister the taxonomy.
			add_action( 'init', array( $this, 'completely_remove_tags' ), 999 );
		}

		/**
		 * Unregister tags taxonomy for posts
		 */
		public function unregister_tags() {
			unregister_taxonomy_for_object_type( 'post_tag', 'post' );
		}

		/**
		 * Completely remove tags taxonomy
		 */
		public function completely_remove_tags() {
			global $wp_taxonomies;
			if ( isset( $wp_taxonomies['post_tag'] ) ) {
				unset( $wp_taxonomies['post_tag'] );
			}
			unregister_taxonomy( 'post_tag' );
		}

		/**
		 * Remove post tag support entirely
		 */
		public function remove_post_tag_support() {
			remove_post_type_support( 'post', 'post-tags' );
		}

		/**
		 * Remove tags submenu from Posts menu
		 */
		public function remove_tags_menu() {
			remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
		}

		/**
		 * Remove tags metabox from post editor
		 */
		public function remove_tags_metabox() {
			remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
			remove_meta_box( 'tagsdiv-post_tag', 'post', 'normal' );
			remove_meta_box( 'tagsdiv-post_tag', 'post', 'advanced' );
			// Also try alternative names.
			remove_meta_box( 'post_tag', 'post', 'side' );
			remove_meta_box( 'post_tagdiv', 'post', 'side' );
			remove_meta_box( 'post_tag', 'post', 'normal' );
			remove_meta_box( 'post_tagdiv', 'post', 'normal' );
		}

		/**
		 * Hide tags metabox with CSS as last resort
		 */
		public function hide_tags_with_css() {
			global $pagenow;

			if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
				echo '<style>
					#tagsdiv-post_tag,
					#post_tag,
					#post_tagdiv,
					.tagsdiv,
					.postbox#tagsdiv-post_tag,
					div[id*="tag"],
					.meta-box-sortables #tagsdiv-post_tag {
						display: none !important;
					}
				</style>';
			}
		}
	}
} // End if class_exists check.

// Initialize the plugin only if the class exists and hasn't been initialized yet.
if ( class_exists( 'CBBlogOptions' ) && ! isset( $GLOBALS['cb_blog_options_instance'] ) ) {
    $GLOBALS['cb_blog_options_instance'] = new CBBlogOptions();
}

// Activation hook.
if ( ! function_exists( 'cb_blog_options_activation_check' ) ) {
    register_activation_hook( __FILE__, array( 'CBBlogOptions', 'activate' ) );
}

// Deactivation hook.
if ( ! function_exists( 'cb_blog_options_deactivation_check' ) ) {
    register_deactivation_hook( __FILE__, array( 'CBBlogOptions', 'deactivate' ) );
}
?>
