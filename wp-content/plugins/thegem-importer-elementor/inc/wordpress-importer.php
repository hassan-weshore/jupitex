<?php
/*
Plugin Name: WordPress Importer
Plugin URI: http://wordpress.org/extend/plugins/wordpress-importer/
Description: Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
Author: wordpressdotorg
Author URI: http://wordpress.org/
Version: 0.6.3
Text Domain: wordpress-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( ! defined( 'WP_LOAD_IMPORTERS' ) )
	return;

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require $class_wp_importer;
}

// include WXR file parsers
require dirname( __FILE__ ) . '/parsers.php';

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */
 if ( class_exists( 'WP_Importer' ) ) {

	class ThegemImportTimeoutException extends Exception
	{
		public function __construct($message, $code = 0, Exception $previous = null) {
			parent::__construct($message, $code, $previous);
		}
	}

	class WP_Import extends WP_Importer {
		var $max_wxr_version = 1.2; // max. supported WXR version

		var $id; // WXR attachment ID

		// information to import from WXR file
		var $version;
		var $authors = array();
		var $posts = array();
		var $terms = array();
		var $categories = array();
		var $tags = array();
		var $base_url = '';

		// mappings from old information to new
		var $processed_authors = array();
		var $author_mapping = array();
		var $processed_terms = array();
		var $processed_terms_by_slug = array();
		var $processed_posts = array();
		var $fake_posts = array();
		var $loaded_fake_posts = array();
		var $post_orphans = array();
		var $loaded_post_orphans = array();
		var $processed_menu_items = array();
		var $menu_item_orphans = array();
		var $missing_menu_items = array();

		var $fetch_attachments = false;
		var $url_remap = array();
		var $featured_images = array();

		var $result_status = 0;

		var $lastSavedAt = 0;
		var $savePeriod = 60*2; // two minutes

		var $imageImporter;

		var $exitBeforeAttachmentImportIfSomePostProcessed = false;
		var $watchPostProcessed = false;
		var $somePostsProcessed = false;

		private static $importFile;

		public function stateLoaded() {
			return $this->lastSavedAt>0;
		}

		public static function setImportDir($dir,$file='import.state') {
			static::$importFile=$dir.'/'.$file;
			write_log('set import dir to '.static::$importFile);
			write_log('set time limit');
			set_time_limit(100);
		}

		function saveState() {
			if (time()-$this->lastSavedAt > $this->savePeriod || serialize($this->fake_posts)!=serialize($this->loaded_fake_posts) || serialize($this->post_orphans)!=serialize($this->loaded_post_orphans)) {
				$this->lastSavedAt=time();
				$data=serialize($this);
				file_put_contents(static::$importFile,$data);
				write_log('save state '.sha1($data).' '.strlen($data));
			}
		}

		static function getInstance() {
			write_log('importer get instance');
			$object=@unserialize(@file_get_contents(static::$importFile));

			if (!$object) {
				write_log('failed to load instance');
				$object=new self;
			} else {
				write_log('instance loaded');
				$object->loaded_fake_posts = $object->fake_posts;
				$object->loaded_post_orphans = $object->post_orphans;
			}

			$object->somePostsProcessed = false;

			return $object;
		}

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the WXR import process
		 */
		function dispatch() {
			$this->header();

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			switch ( $step ) {
				case 0:
					$this->greet();
					break;
				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() )
						$this->import_options();
					break;
				case 2:
					check_admin_referer( 'import-wordpress' );
					$this->fetch_attachments = ( ! empty( $_POST['fetch_attachments'] ) && $this->allow_fetch_attachments() );
					$this->id = (int) $_POST['import_id'];
					$file = get_attached_file( $this->id );
					set_time_limit(0);
					$this->import( $file );
					break;
			}

			$this->footer();
		}

		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import( $file, $ids='' ) {
			thegem_init_elementor_importer();
			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

			if (!$this->imageImporter) {
				$this->imageImporter = \Elementor\Plugin::$instance->templates_manager->get_import_images_instance();
			}

			if($ids!=='') {
				$ids_array = $ids;
			} else {
				$ids_array = array();
			}

			$this->result_status = 0;

			if (!$this->stateLoaded()) {
				$this->import_start( $file );

				$this->get_author_mapping();

				wp_suspend_cache_invalidation( true );
				$this->process_categories();
				$this->process_tags();
				$this->process_terms();
			} else {
				wp_suspend_cache_invalidation( true );
			}

			try {
				$this->process_posts($ids_array);
			} catch(ThegemImportTimeoutException $e) {
			}

			wp_suspend_cache_invalidation( false );

			// update incorrect/missing information in the DB

			if (empty($ids_array)) $this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();

			$this->import_end();
			if($this->result_status !== 0) {
				return $this->result_status;
			}
		}

		function import_singles( $file, $ids='') {
			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) ,10, 2 );
			add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

			if($ids!=='') {
				$ids_array = $ids;
			} else {
				$ids_array = array();
			}

			$this->import_start( $file );

			$this->get_author_mapping();

			wp_suspend_cache_invalidation( true );
			$this->process_categories();
			$this->process_tags();
			$this->process_terms();

			try {
				$this->process_posts($ids_array);
			} catch(ThegemImportTimeoutException $e) {
			}

			wp_suspend_cache_invalidation( false );

			// update incorrect/missing information in the DB
			if (empty($ids_array)) $this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();

			$this->import_end();
		}


		/**
		 * Parses the WXR file and prepares us for the task of processing parsed data
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import_start( $file ) {

			if (!is_array($file) && !is_file($file)) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'wordpress-importer' ) . '</p>';
				$this->footer();
				die();
			}

			$import_data = $this->parse( $file );

			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $import_data->get_error_message() ) . '</p>';
				$this->footer();
				die();
			}

			$this->version = $import_data['version'];
			$this->get_authors_from_import( $import_data );
			$this->posts = $import_data['posts'];

			//file_put_contents('/var/www/html/wp-content/data.txt',var_export($this->posts,true));

			$this->terms = $import_data['terms'];
			$this->categories = $import_data['categories'];
			$this->tags = $import_data['tags'];
			$this->base_url = esc_url( $import_data['base_url'] );

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			do_action( 'import_start' );
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		function import_end() {
			wp_import_cleanup( $this->id );

			$data = get_option('thegem_import_posts_map');

			if (!is_array($data)) {
				$data = [];
			}

			foreach($this->processed_posts as $k=>$v) {
				$data[$k] = $v;
			} 

			update_option('thegem_import_posts_map',$data);

			wp_cache_flush();
			foreach ( get_taxonomies() as $tax ) {
				delete_option( "{$tax}_children" );
				_get_term_hierarchy( $tax );
			}

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			echo '<p>' . __( 'All done.', 'wordpress-importer' ) . ' <a href="' . admin_url() . '">' . __( 'Have fun!', 'wordpress-importer' ) . '</a>' . '</p>';
			echo '<p>' . __( 'Remember to update the passwords and roles of imported users.', 'wordpress-importer' ) . '</p>';

			do_action( 'import_end' );
		}

		/**
		 * Handles the WXR upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		function handle_upload() {
			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $file['error'] ) . '</p>';
				return false;
			} else if ( ! file_exists( $file['file'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'wordpress-importer' ), esc_html( $file['file'] ) );
				echo '</p>';
				return false;
			}

			$this->id = (int) $file['id'];
			$import_data = $this->parse( $file['file'] );
			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo esc_html( $import_data->get_error_message() ) . '</p>';
				return false;
			}

			$this->version = $import_data['version'];
			if ( $this->version > $this->max_wxr_version ) {
				echo '<div class="error"><p><strong>';
				printf( __( 'This WXR file (version %s) may not be supported by this version of the importer. Please consider updating.', 'wordpress-importer' ), esc_html($import_data['version']) );
				echo '</strong></p></div>';
			}

			$this->get_authors_from_import( $import_data );

			return true;
		}

		/**
		 * Retrieve authors from parsed WXR data
		 *
		 * Uses the provided author information from WXR 1.1 files
		 * or extracts info from each post for WXR 1.0 files
		 *
		 * @param array $import_data Data returned by a WXR parser
		 */
		function get_authors_from_import( $import_data ) {
			if ( ! empty( $import_data['authors'] ) ) {
				$this->authors = $import_data['authors'];
				// no author information, grab it from the posts
			} else {
				foreach ( $import_data['posts'] as $post ) {
					$login = sanitize_user( $post['post_author'], true );
					if ( empty( $login ) ) {
						printf( __( 'Failed to import author %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html( $post['post_author'] ) );
						echo '<br />';
						continue;
					}

					if ( ! isset($this->authors[$login]) )
						$this->authors[$login] = array(
							'author_login' => $login,
							'author_display_name' => $post['post_author']
						);
				}
			}
		}

		/**
		 * Display pre-import options, author importing/mapping and option to
		 * fetch attachments
		 */
		function import_options() {
			$j = 0;
			?>
			<form action="<?php echo admin_url( 'admin.php?import=wordpress&amp;step=2' ); ?>" method="post">
				<?php wp_nonce_field( 'import-wordpress' ); ?>
				<input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />

				<?php if ( ! empty( $this->authors ) ) : ?>
					<h3><?php _e( 'Assign Authors', 'wordpress-importer' ); ?></h3>
					<p><?php _e( 'To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'wordpress-importer' ); ?></p>
					<?php if ( $this->allow_create_users() ) : ?>
						<p><?php printf( __( 'If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'wordpress-importer' ), esc_html( get_option('default_role') ) ); ?></p>
					<?php endif; ?>
					<ol id="authors">
						<?php foreach ( $this->authors as $author ) : ?>
							<li><?php $this->author_select( $j++, $author ); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<?php if ( $this->allow_fetch_attachments() ) : ?>
					<h3><?php _e( 'Import Attachments', 'wordpress-importer' ); ?></h3>
					<p>
						<input type="checkbox" value="1" name="fetch_attachments" id="import-attachments" />
						<label for="import-attachments"><?php _e( 'Download and import file attachments', 'wordpress-importer' ); ?></label>
					</p>
				<?php endif; ?>

				<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'wordpress-importer' ); ?>" /></p>
			</form>
			<?php
		}

		/**
		 * Display import options for an individual author. That is, either create
		 * a new user based on import info or map to an existing user
		 *
		 * @param int $n Index for each author in the form
		 * @param array $author Author information, e.g. login, display name, email
		 */
		function author_select( $n, $author ) {
			_e( 'Import author:', 'wordpress-importer' );
			echo ' <strong>' . esc_html( $author['author_display_name'] );
			if ( $this->version != '1.0' ) echo ' (' . esc_html( $author['author_login'] ) . ')';
			echo '</strong><br />';

			if ( $this->version != '1.0' )
				echo '<div style="margin-left:18px">';

			$create_users = $this->allow_create_users();
			if ( $create_users ) {
				if ( $this->version != '1.0' ) {
					_e( 'or create new user with login name:', 'wordpress-importer' );
					$value = '';
				} else {
					_e( 'as a new user:', 'wordpress-importer' );
					$value = esc_attr( sanitize_user( $author['author_login'], true ) );
				}

				echo ' <input type="text" name="user_new['.$n.']" value="'. $value .'" /><br />';
			}

			if ( ! $create_users && $this->version == '1.0' )
				_e( 'assign posts to an existing user:', 'wordpress-importer' );
			else
				_e( 'or assign posts to an existing user:', 'wordpress-importer' );
			wp_dropdown_users( array( 'name' => "user_map[$n]", 'multi' => true, 'show_option_all' => __( '- Select -', 'wordpress-importer' ) ) );
			echo '<input type="hidden" name="imported_authors['.$n.']" value="' . esc_attr( $author['author_login'] ) . '" />';

			if ( $this->version != '1.0' )
				echo '</div>';
		}

		/**
		 * Map old author logins to local user IDs based on decisions made
		 * in import options form. Can map to an existing user, create a new user
		 * or falls back to the current user in case of error with either of the previous
		 */
		function get_author_mapping() {
			if ( ! isset( $_POST['imported_authors'] ) )
				return;

			$create_users = $this->allow_create_users();

			foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
				// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
				$santized_old_login = sanitize_user( $old_login, true );
				$old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

				if ( ! empty( $_POST['user_map'][$i] ) ) {
					$user = get_userdata( intval($_POST['user_map'][$i]) );
					if ( isset( $user->ID ) ) {
						if ( $old_id )
							$this->processed_authors[$old_id] = $user->ID;
						$this->author_mapping[$santized_old_login] = $user->ID;
					}
				} else if ( $create_users ) {
					if ( ! empty($_POST['user_new'][$i]) ) {
						$user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
					} else if ( $this->version != '1.0' ) {
						$user_data = array(
							'user_login' => $old_login,
							'user_pass' => wp_generate_password(),
							'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
							'display_name' => $this->authors[$old_login]['author_display_name'],
							'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
							'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
						);
						$user_id = wp_insert_user( $user_data );
					}

					if ( ! is_wp_error( $user_id ) ) {
						if ( $old_id )
							$this->processed_authors[$old_id] = $user_id;
						$this->author_mapping[$santized_old_login] = $user_id;
					} else {
						printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html($this->authors[$old_login]['author_display_name']) );
						if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
							echo ' ' . $user_id->get_error_message();
						echo '<br />';
					}
				}

				// failsafe: if the user_id was invalid, default to the current user
				if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = (int) get_current_user_id();
					$this->author_mapping[$santized_old_login] = (int) get_current_user_id();
				}
			}
		}

		/**
		 * Create new categories based on import information
		 *
		 * Doesn't create a new category if its slug already exists
		 */
		function process_categories() {
			$this->categories = apply_filters( 'wp_import_categories', $this->categories );

			if ( empty( $this->categories ) )
				return;

			foreach ( $this->categories as $cat ) {
				// if the category already exists leave it alone
				$term_id = term_exists( $cat['category_nicename'], 'category' );
				if ( $term_id ) {
					if ( is_array($term_id) ) $term_id = $term_id['term_id'];
					if ( isset($cat['term_id']) )
						$this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
					continue;
				}

				$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
				$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
				$catarr = array(
					'category_nicename' => $cat['category_nicename'],
					'category_parent' => $category_parent,
					'cat_name' => $cat['cat_name'],
					'category_description' => $category_description
				);
				$catarr = wp_slash( $catarr );

				$id = wp_insert_category( $catarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset($cat['term_id']) )
						$this->processed_terms[intval($cat['term_id'])] = $id;
				} else {
					printf( __( 'Failed to import category %s', 'wordpress-importer' ), esc_html($cat['category_nicename']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ': ' . $id->get_error_message();
					echo '<br />';
					continue;
				}

				$this->process_termmeta( $cat, $id );
			}

			unset( $this->categories );
		}

		/**
		 * Create new post tags based on import information
		 *
		 * Doesn't create a tag if its slug already exists
		 */
		function process_tags() {
			$this->tags = apply_filters( 'wp_import_tags', $this->tags );

			if ( empty( $this->tags ) )
				return;

			foreach ( $this->tags as $tag ) {
				// if the tag already exists leave it alone
				$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
				if ( $term_id ) {
					if ( is_array($term_id) ) $term_id = $term_id['term_id'];
					if ( isset($tag['term_id']) )
						$this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
						$this->processed_terms_by_slug['post_tag'][$tag['term_slug']] = (int) $term_id;
					continue;
				}

				$tag = wp_slash( $tag );
				$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
				$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

				$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset($tag['term_id']) )
						$this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
						$this->processed_terms_by_slug['post_tag'][$tag['tag_slug']] = (int) $term_id;
				} else {
					printf( __( 'Failed to import post tag %s', 'wordpress-importer' ), esc_html($tag['tag_name']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ': ' . $id->get_error_message();
					echo '<br />';
					continue;
				}

				$this->process_termmeta( $tag, $id['term_id'] );
			}

			//unset( $this->tags );
		}

		/**
		 * Create new terms based on import information
		 *
		 * Doesn't create a term its slug already exists
		 */
		function process_terms($slug_array = null, $slug_taxonomy = null, $ids_array = null) {
			$this->terms = apply_filters( 'wp_import_terms', $this->terms );

			if ( empty( $this->terms ) )
				return;

			write_log(">>> process_terms ".json_encode($slug_array).",$slug_taxonomy,".json_encode($ids_array));

			foreach ( $this->terms as $term ) {
				if ($slug_taxonomy != null && ($term['term_taxonomy'] != $slug_taxonomy || !in_array($term['slug'], $slug_array))) {
					continue;
				}

				if ($ids_array != null && !in_array($term['term_id'], $ids_array)) {
					continue;
				}

				// if the term already exists in the correct taxonomy leave it alone
				if (isset($this->processed_terms_by_slug[$term['term_taxonomy']]) && isset($this->processed_terms_by_slug[$term['term_taxonomy']][$term['slug']])) {
					continue;
				}

				$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
				if ( $term_id ) {
					if ( is_array($term_id) ) $term_id = $term_id['term_id'];
					if ( isset($term['term_id']) )
						$this->processed_terms[intval($term['term_id'])] = (int) $term_id;
						$this->processed_terms_by_slug[$term['term_taxonomy']][$term['slug']] = (int) $term_id;
					continue;
				}

				if ( empty( $term['term_parent'] ) ) {
					$parent = 0;
				} else {
					if (!isset($this->processed_terms[$term['term_parent']])) {
						$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
						if ( is_array( $parent ) ) {
							$parent = $parent['term_id'];
						} else {
							$this->process_terms(array($term['term_parent']), $term['term_taxonomy'] );
						}
					}
				}

				$term = wp_slash( $term );
				$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
				$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

				$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
				if ( ! is_wp_error( $id ) ) {
					if ( isset($term['term_id']) )
						$this->processed_terms[intval($term['term_id'])] = $id['term_id'];
						$this->processed_terms_by_slug[$term['term_taxonomy']][$term['slug']] = (int) $term_id;
				} else {
					printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html($term['term_taxonomy']), esc_html($term['term_name']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ': ' . $id->get_error_message();
					echo '<br />';
					continue;
				}

				$this->process_termmeta( $term, $id['term_id'] );
			}

			write_log("<<< process_terms ".json_encode($slug_array).",$slug_taxonomy,".json_encode($ids_array));

			//unset( $this->terms );
		}

		/**
		 * Add metadata to imported term.
		 *
		 * @since 0.6.2
		 *
		 * @param array $term	Term data from WXR import.
		 * @param int   $term_id ID of the newly created term.
		 */
		protected function process_termmeta( $term, $term_id ) {
			if ( ! isset( $term['termmeta'] ) ) {
				$term['termmeta'] = array();
			}
			$term['termmeta'] = wp_unslash( $term['termmeta'] );
			/**
			 * Filters the metadata attached to an imported term.
			 *
			 * @since 0.6.2
			 *
			 * @param array $termmeta Array of term meta.
			 * @param int   $term_id  ID of the newly created term.
			 * @param array $term	 Term data from the WXR import.
			 */
			$term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

			if ( empty( $term['termmeta'] ) ) {
				return;
			}

			foreach ( $term['termmeta'] as $meta ) {
				/**
				 * Filters the meta key for an imported piece of term meta.
				 *
				 * @since 0.6.2
				 *
				 * @param string $meta_key Meta key.
				 * @param int	$term_id  ID of the newly created term.
				 * @param array  $term	 Term data from the WXR import.
				 */
				$key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
				if ( ! $key ) {
					continue;
				}

				// Export gets meta straight from the DB so could have a serialized string
				$value = maybe_unserialize( $meta['value'] );
				if(empty($value) && !empty($meta['value'])) {
					$value = maybe_unserialize( str_replace("\n", "\r\n", $meta['value']) );
				}

				add_term_meta( $term_id, $key, $value );

				/**
				 * Fires after term meta is imported.
				 *
				 * @since 0.6.2
				 *
				 * @param int	$term_id ID of the newly created term.
				 * @param string $key	 Meta key.
				 * @param mixed  $value   Meta value.
				 */
				do_action( 'import_term_meta', $term_id, $key, $value );
			}
		}

		static function process_content_links_variations($links, $modifyLink) {
			$res = array();

			foreach($links as $link) {
				$res[] = $link;
				$res[] = static::process_content_links_escape($link);

				$linkHttps = $modifyLink ? preg_replace('%^http:%','https:',$link):$link;
				$res[] = $linkHttps;
				$res[] = static::process_content_links_escape($linkHttps);

				$linkHttp = $modifyLink ? preg_replace('%^https:%','http:',$link):$link;
				$res[] = $linkHttp;
				$res[] = static::process_content_links_escape($linkHttp);
				$res[] = rawurlencode($linkHttp);
			}

			return $res;
		}

		static function process_content_links_escape($value) {
		    return str_replace('/','\\/',$value);
		}

		static function process_content_links($value) {
			static $src, $dst;

			if (!isset($src)) {
				$import_data = get_option('thegem_importer_data');
				$upload_dir = wp_upload_dir();
				$import_selection = get_option('thegem_importer_selection');
				$imports_data = get_option('thegem_importer_imports_data');
				$demo_url = $imports_data[$import_selection['import']]['demo-link'];

				$src = array(
					$import_data['uploads_url'],
					$import_data['theme_url'],
					$demo_url,
				);

				$dst = array(
					$upload_dir['baseurl'],
					get_template_directory_uri(),
					get_site_url().'/',
				);

				$src = static::process_content_links_variations($src, true);
				$dst = static::process_content_links_variations($dst, false);
			}

			if (is_array($value)) {
			    foreach($value as $k=>$v) {
					$value[$k] = static::process_content_links($v);
			    }
			} elseif (is_string($value)) {
				$value = str_replace($src, $dst, $value);
			}

			return $value;
		}

		function process_terms_with_contents($slug, $taxonomy, $post_type = '') {
			write_log(">>> process_term_with_contents $slug $taxonomy");

			$this->process_terms(array($slug), $taxonomy);

			$ids = array();

			foreach($this->posts as $post) {
				$import = is_array($post) && $slug=='0' && $post['post_type'] == $post_type;
				if (is_array($post) && isset($post['terms'])) {
					foreach($post['terms'] as $term) {
						if ($term['slug'] == $slug) {
							$import = true;
						}
					}
				}
				if ($import) {
					$ids[]=$post['post_id'];
				}
			}

			if (!empty($ids)) {
				$this->process_posts($ids);
			}

			write_log("<<< process_term_with_contents $slug $taxonomy");
		}

		function import_post_elementor_images_helper(&$data, $top_level=true) {
			if ($top_level) {
				$lastPostId = $this->get_last_post_id();
			}

			foreach($data as $k=>&$v) {
				// preimport images in elementor data
				if (strpos($k,'image')!==false && isset($v['url'])) {
					if (thegem_importer_time_ended()) {
						$this->result_status = 10;
	
						$this->saveState();
	
						throw new ThegemImportTimeoutException('');
					}
	
					$st = microtime(true);
					
					$data[$k]=$this->imageImporter->import($v);
					write_log('image '.$v['url'].' imported in '.(microtime(true)-$st). " seconds, post $lastPostId - ".$this->get_last_post_id());
				}

				if (is_array($v)) {
					$this->import_post_elementor_images_helper($v, false);
				}
			}

			if ($top_level) {
				$this->media_add_suffix($lastPostId);
			}
		}
		
		function import_post_elementor_dependencies_helper(&$data) {
			if (isset($data['widgetType'])) {
				switch ($data['widgetType']) {
					case 'thegem-extended-products-grid':
						write_log('found '.$data['widgetType']);
						write_log('import products for grid '.json_encode($data['settings']['import_products']));
						if (isset($data['settings']['import_products'])) {
							$this->process_posts($data['settings']['import_products']);
						}
						break;
					case 'thegem-contact-form7':
						write_log('found '.$data['widgetType'].' with form_id '.$data['settings']['form_id']);
						if ($data['settings']['form_id']) {
							$this->process_posts(array($data['settings']['form_id']));
							$data['settings']['form_id']=$this->processed_posts[$data['settings']['form_id']];
						}
						break;

					case 'thegem-cta':
					case 'thegem-styled-textbox':	
						write_log('found '.$data['widgetType'].' with source '.$data['settings']['source'].' and template '.$data['settings']['template']);
						if ($data['settings']['source'] == 'template' && $data['settings']['template']) {
							$this->process_posts(array($data['settings']['template']));
							$data['settings']['template']=$this->processed_posts[$data['settings']['template']];
						}
						break;
	
					case 'thegem-accordion':
					case 'thegem-tabs':
							write_log('found '.$data['widgetType']);
						if (is_array($data['settings']['tabs'])) {
							$ids = array();
							foreach($data['settings']['tabs'] as $key=>$tab) {
								if ($tab['source'] == 'template' && $tab['template']) {
									$ids[]=$tab['template'];
								}
							}
							
							if (!empty($ids)) {
								$this->process_posts($ids);
								foreach($data['settings']['tabs'] as $key=>$tab) {
									if ($tab['source'] == 'template' && $tab['template']) {
										$data['settings']['tabs'][$key]['template'] = $this->processed_posts[$tab['template']];
									}
								}
							}
						}
						break;

					case 'thegem-blog-grid':
					case 'thegem-bloglist':
					case 'thegem-blogslider':
					case 'thegem-blogtimeline':
						write_log('found '.$data['widgetType'].' with select_blog_cat '.json_encode($data['settings']['select_blog_cat']));
						foreach($data['settings']['select_blog_cat'] as $cat) {
							$this->process_terms_with_contents($cat, 'category', 'post');
						}
						break;

					case 'thegem-featured-posts-slider':	
						write_log('found '.$data['widgetType'].' with categories '.json_encode($data['settings']['categories']));
						foreach($data['settings']['categories'] as $cat) {
							$this->process_terms_with_contents($cat, 'category', 'post');
						}
						break;

					case 'thegem-extended-blog-grid':
						write_log('found '.$data['widgetType'].' with categories '.json_encode($data['settings']['categories']));
						foreach($data['settings']['categories'] as $cat) {
							$this->process_terms_with_contents($cat, 'category', 'post');
						}
						break;
	
					case 'thegem-portfolio':
					case 'thegem-portfolio-list':
					case 'thegem-portfolio-slider':
						write_log('found '.$data['widgetType'].' with content_portfolios_cat '.json_encode($data['settings']['content_portfolios_cat']));
						foreach($data['settings']['content_portfolios_cat'] as $cat) {
							$this->process_terms_with_contents($cat, 'thegem_portfolios', 'thegem_pf_item');
						}
						break;
	
					case 'thegem-team':
						write_log('found '.$data['widgetType'].' with content_team_cat '.json_encode($data['settings']['content_team_cat']));
						foreach($data['settings']['content_team_cat'] as $cat) {
							$this->process_terms_with_contents($cat, 'thegem_teams', 'thegem_team_person');
						}
						break;

					case 'thegem-counter':
						if (isset($data['settings']['counter_team_settings']) && $data['settings']['counter_team_settings']) {
							write_log('found '.$data['widgetType'].' with counter_team_settings '.$data['settings']['counter_team_settings']);
							$this->process_posts(array($data['settings']['counter_team_settings']));
							$data['settings']['counter_team_settings']=$this->processed_posts[$data['settings']['counter_team_settings']];
						}
						if (isset($data['settings']['content_team_all_persons']) && $data['settings']['content_team_all_persons']) {
							write_log('found '.$data['widgetType'].' with content_team_all_persons '.$data['settings']['content_team_all_persons']);
	  					     $this->process_posts(array($data['settings']['content_team_all_persons']));
							$data['settings']['content_team_all_persons']=$this->processed_posts[$data['settings']['content_team_all_persons']];
						}
						break;

					case 'thegem-testimonials':
						write_log('found '.$data['widgetType'].' with content_testimonials_cat '.json_encode($data['settings']['content_testimonials_cat']));
						foreach($data['settings']['content_testimonials_cat'] as $cat) {
							$this->process_terms_with_contents($cat, 'thegem_testimonials_sets','thegem_testimonial');
						}
						break;

					case 'thegem-products-grid':
					case 'thegem-products-slider':
						write_log('found '.$data['widgetType'].' with content_products_cat '.json_encode($data['settings']['content_products_cat']));
						foreach($data['settings']['content_products_cat'] as $cat) {
							$this->process_terms_with_contents($cat, 'product_cat', 'product');
						}
						break;
				}	
			}

			foreach($data as &$v) {
				if (is_array($v)) {
					$this->import_post_elementor_dependencies_helper($v);
				}
			}
		}

		function get_last_post_id() {
			global $wpdb;
			
			$result = $wpdb->get_results("SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1");
			$row = $result[0];
			$id = $row->ID;
		
			return $id;
		}
		
		function media_add_suffix($lastPostId) {
			global $wpdb;
			if ($lastPostId>0) {
				$wpdb->query("update $wpdb->posts set post_title=concat(post_title,' (Demo)') where ID>".esc_sql($lastPostId));
			}
		}
			
		function import_post_elementor_dependencies(&$post) {
			static $stack=[];

			static $import_media;
			if (!isset($import_media)) {
				$import_selection = get_option('thegem_importer_selection');
				$import_media = $import_selection['import_media'] ? true : false;
			}

			write_log('>>> import_post_elementor_dependencies '.$post['post_id']);

			if ( isset($post['postmeta']) ) {
				foreach($post['postmeta'] as $k=>$v) {
					switch ($v['key']) {
						case '_elementor_data':
							$elementor_data = json_decode($v['value'], true);
							$this->import_post_elementor_images_helper($elementor_data);
							$post['postmeta'][$k]['value'] = json_encode($elementor_data);
							break;
					}
				}
			}

			if (array_search($post['post_id'],$stack) !== false) {
				write_log('CIRCULAR DEPENDENCIES FOUND, POSTS STACK: '.implode(',',$stack));

				$fakePostData = array(
					'import_id' => $post['post_id'],
					'post_title' => 'Import tmp '.$post['post_id'].' (Demo)',
				);
				$fakePostId = wp_insert_post( $fakePostData);

				write_log('CREATED FAKE POST '.$fakePostId);
				write_log('<<< import_post_elementor_dependencies '.$post['post_id']);
				$this->processed_posts[$post['post_id']] = $fakePostId;
				$this->fake_posts[$post['post_id']] = $fakePostId;
				return;
			}

			array_push($stack,$post['post_id']);

			if ( isset($post['postmeta']) ) {
				foreach($post['postmeta'] as $k=>$v) {
					switch ($v['key']) {
						case '_elementor_data':
							$elementor_data = json_decode($v['value'], true);
							$this->import_post_elementor_dependencies_helper($elementor_data);
							$post['postmeta'][$k]['value'] = json_encode($elementor_data);
							break;

						case '_thumbnail_id':
							write_log('found _thumbnail_id with value '.$v['value']);
							if ($v['value']) {
								$this->process_posts(array($v['value']));
								$post['postmeta'][$k]['value'] = $this->processed_posts[$v['value']];
							}
							break;

						case '_product_image_gallery':
							write_log('found _product_image_gallery with value '.$v['value']);
							$ids=preg_split('%,%',$v['value'],-1,PREG_SPLIT_NO_EMPTY);
							if (!empty($ids)) {
								$this->process_posts($ids);
								$newIds=array();
								foreach($ids as $id) {
									$newIds[] = $this->processed_posts[$id];
								}
								$post['postmeta'][$k]['value'] = implode(',', $newIds);
							}
							break;
	
						case 'thegem_page_data':
							$data = unserialize( $v['value'] );
							if($data === false && !empty($v['value'])) {
								$data = unserialize( str_replace("\n", "\r\n", $v['value']) );
							}

							if($import_media && !empty($data['title_background_image'])) {
								write_log('found thegem_page_data with title_background_image '.$data['title_background_image']);
								foreach($this->posts as $aPost) {
									if (is_array($aPost) && isset($aPost['attachment_url']) && $aPost['attachment_url']==$data['title_background_image']) {
										write_log('FOUND POST '.$aPost['post_id']);
										$this->process_posts(array($aPost['post_id']));
									}
								}
								$data['title_background_image'] = $this->process_content_links($data['title_background_image']);
							}

							if ($data['title_style'] == 2 && $data['title_template']) {
								write_log('found thegem_page_data with title_style 2 and title_template '.$data['title_template']);
								$this->process_posts(array($data['title_template']));
								$data['title_template'] = $this->processed_posts[$data['title_template']];
							}

							if ($data['footer_custom']) {
								write_log('found thegem_page_data with footer_custom '.$data['footer_custom']);
								$this->process_posts(array($data['footer_custom']));
								$data['footer_custom'] = $this->processed_posts[$data['footer_custom']];
							}

							$post['postmeta'][$k]['value']=serialize($data);

							break;
					}
				}
			}

			array_pop($stack);
			write_log('<<< import_post_elementor_dependencies '.$post['post_id']);
		}

		function create_woo_taxonomy($taxonomy)
		{
			$attribute_name = wc_attribute_taxonomy_slug( $taxonomy );

			// Create the taxonomy.
			if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies(), true ) ) {
				wc_create_attribute(
					array(
						'name'         => ucfirst($attribute_name),
						'slug'         => $attribute_name,
						'type'         => 'select',
						'order_by'     => 'menu_order',
						'has_archives' => false,
					)
				);
			}

			// Register the taxonomy now so that the import works!
			register_taxonomy(
				$taxonomy,
				apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy, array( 'product' ) ),
				apply_filters(
					'woocommerce_taxonomy_args_' . $taxonomy,
					array(
						'hierarchical' => true,
						'show_ui'      => false,
						'query_var'    => true,
						'rewrite'      => false,
					)
				)
			);
		}

		/**
		 * Create new posts based on import information
		 *
		 * Posts marked as having a parent which doesn't exist will become top level items.
		 * Doesn't create a new post if: the post type doesn't exist, the given post ID
		 * is already noted as imported or a post with the same title and date already exists.
		 * Note that new/updated terms, comments and meta are imported for the last of the above.
		 */
		function process_posts($ids_array = array()) {
			write_log('>>> process_posts '.implode(',',$ids_array));

			if (!$this->stateLoaded()) {
				$this->posts = apply_filters( 'wp_import_posts', $this->posts );
			}

			$post_types = get_post_types( array( 'can_export' => true ) );

			$status = get_option('thegem_importer_progress');
			$status['total']=count($this->posts);

			static $import_media;
			if (!isset($import_media)) {
				$import_selection = get_option('thegem_importer_selection');
				$import_media = $import_selection['import_media'] ? true : false;
			}

			foreach ( $this->posts as $postKey=>$post ) {
				if ($post['post_type'] == 'elementor_library' && strpos($post['post_title'], 'Default Kit')!==false) {
					write_log('skip import elementor library');
					$this->posts[$postKey] = true;
					continue;
				}
				
				if ($post===true) {
					continue;
				}

				if (thegem_importer_time_ended()) {
					$this->result_status = 10;
					if ( 'nav_menu_item' == $post['post_type'] ) {
						$this->result_status = 20;
					}

					$this->saveState();

					throw new ThegemImportTimeoutException('');
				}
				
				$post = apply_filters( 'wp_import_post_data_raw', $post );

				//if (!empty($ids_array) && !in_array($post['post_id'], $ids_array) && ($post['post_type'] === 'post' || $post['post_type'] === 'page' || $post['post_type'] === 'product' || $post['post_type'] === 'nav_menu_item' || $post['post_type'] === 'attachment' || $post['post_type'] === 'thegem_pf_item')) continue;
				if (!empty($ids_array) && !in_array($post['post_id'], $ids_array) && in_array($post['post_type'], $post_types)) continue;

				$post['post_content'] = $this->process_content_links($post['post_content']);
				
				write_log('import post '.$post["post_id"].' post_type '.$post['post_type']);

				if ($postKey+1>$status['current']) {
					$status['current'] = $postKey+1;
					update_option('thegem_importer_progress', $status);
				}

				if ( ! post_type_exists( $post['post_type'] ) ) {
					printf( __( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer' ),
						esc_html($post['post_title']), esc_html($post['post_type']) );
					echo '<br />';
					do_action( 'wp_import_post_exists', $post );
					$this->posts[$postKey]=true;
					continue;
				}

				if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) ) {
					$this->posts[$postKey]=true;
					continue;
				}

				if ( $post['status'] == 'auto-draft' ) {
					$this->posts[$postKey]=true;
					continue;
				}

				if ( 'nav_menu_item' == $post['post_type'] ) {
					$this->process_menu_item( $post );
					$this->posts[$postKey]=true;
					continue;
				}

				$post_type_object = get_post_type_object( $post['post_type'] );

				$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );

				/**
				 * Filter ID of the existing post corresponding to post currently importing.
				 *
				 * Return 0 to force the post to be imported. Filter the ID to be something else
				 * to override which existing post is mapped to the imported post.
				 *
				 * @see post_exists()
				 * @since 0.6.2
				 *
				 * @param int   $post_exists  Post ID, or 0 if post did not exist.
				 * @param array $post		 The post array to be inserted.
				 */
				$post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );
				$exists_stop = false;

				if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
					printf( __('%s &#8220;%s&#8221; already exists.', 'wordpress-importer'), $post_type_object->labels->singular_name, esc_html($post['post_title']) );
					echo '<br />';
					$comment_post_ID = $post_id = $post_exists;
					$this->processed_posts[ intval( $post['post_id'] ) ] = intval( $post_exists );
					$exists_stop = true;
				} else {
					$this->import_post_elementor_dependencies($post);

					$post_parent = (int) $post['post_parent'];
					if ( $post_parent ) {
						// if we already know the parent, map it to the new local ID
						if ( isset( $this->processed_posts[$post_parent] ) ) {
							$post_parent = $this->processed_posts[$post_parent];
							// otherwise record the parent for later
						} else {
							$this->post_orphans[intval($post['post_id'])] = $post_parent;
							write_log('post '.intval($post['post_id']).' has orphan parent '.$post_parent);
							$post_parent = 0;
						}
					}

					// map the post author
					$author = sanitize_user( $post['post_author'], true );
					if ( isset( $this->author_mapping[$author] ) )
						$author = $this->author_mapping[$author];
					else
						$author = (int) get_current_user_id();

					$postdata = array(
						'post_author' => $author, 'post_date' => $post['post_date'],
						'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
						'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
						'post_status' => $post['status'], 'post_name' => $post['post_name'],
						'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
						'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
						'post_type' => $post['post_type'], 'post_password' => $post['post_password']
					);

					if (!isset($this->fake_posts[$post['post_id']])) {
						$postdata['import_id'] = $post['post_id'];
					} else {
						$postdata['ID'] = $this->fake_posts[$post['post_id']];
						write_log('updating fake post '.$post['post_id'].', new id '.$this->fake_posts[$post['post_id']]);
						unset($this->fake_posts[$post['post_id']]);
					}

					$original_post_ID = $post['post_id'];
					$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

					$postdata = wp_slash( $postdata );

					if ( 'attachment' == $postdata['post_type'] ) {
						$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

						// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
						// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
						$postdata['upload_date'] = $post['post_date'];
						if ( isset( $post['postmeta'] ) ) {
							foreach( $post['postmeta'] as $meta ) {
								if ( $meta['key'] == '_wp_attached_file' ) {
									if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
										$postdata['upload_date'] = $matches[0];
									break;
								}
							}
						}

						if ($this->exitBeforeAttachmentImportIfSomePostProcessed &&
							$this->watchPostProcessed && $this->somePostsProcessed) {
							write_log('exit before process attachment');

							$this->result_status = 10;
							$this->saveState();
							throw new ThegemImportTimeoutException('');
						}
						write_log('process attachment');
						$st = microtime(true);
						$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
						write_log('attachment processed in '.(microtime(true)-$st));
					} else {
						$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
						do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
					}

					if ( is_wp_error( $post_id ) ) {
						printf( __( 'Failed to import %s &#8220;%s&#8221;', 'wordpress-importer' ),
							$post_type_object->labels->singular_name, esc_html($post['post_title']) );
						if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
							echo ': ' . $post_id->get_error_message();
						echo '<br />';
						continue;
					}

					if ( $post['is_sticky'] == 1 )
						stick_post( $post_id );

					write_log('somePostsProcessed mark');	
					$this->somePostsProcessed = true;
				}

				// map pre-import ID to local ID
				$this->processed_posts[intval($post['post_id'])] = (int) $post_id;
				write_log('mark post '.intval($post['post_id']). ' as processed ');
				$this->posts[$postKey]=true;

				if ( ! isset( $post['terms'] ) )
					$post['terms'] = array();

				$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

				// add categories, tags and other terms
				if ( ! empty( $post['terms'] ) ) {
					$terms_to_set = array();
					foreach ( $post['terms'] as $term ) {
						// back compat with WXR 1.0 map 'tag' to 'post_tag'
						$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
						if (!taxonomy_exists($taxonomy) && preg_match('%^pa_%',$taxonomy)) {
							write_log("found unregistered $taxonomy taxonomy");

							$this->create_woo_taxonomy($term['domain']);	
						}

						$term_exists = term_exists( $term['slug'], $taxonomy );
						$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
						if ( ! $term_id ) {
							$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
								do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
							} else {
								printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html($taxonomy), esc_html($term['name']) );
								if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
									echo ': ' . $t->get_error_message();
								echo '<br />';
								do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
								continue;
							}
						}
						$terms_to_set[$taxonomy][] = intval( $term_id );
					}

					foreach ( $terms_to_set as $tax => $ids ) {
						$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
						do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
					}
					unset( $post['terms'], $terms_to_set );
				}

				if ( ! isset( $post['comments'] ) )
					$post['comments'] = array();

				$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

				// add/update comments
				if ( ! empty( $post['comments'] ) ) {
					$num_comments = 0;
					$inserted_comments = array();
					foreach ( $post['comments'] as $comment ) {
						$comment_id	= $comment['comment_id'];
						$newcomments[$comment_id]['comment_post_ID']	  = $comment_post_ID;
						$newcomments[$comment_id]['comment_author']	   = $comment['comment_author'];
						$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
						$newcomments[$comment_id]['comment_author_IP']	= $comment['comment_author_IP'];
						$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
						$newcomments[$comment_id]['comment_date']		 = $comment['comment_date'];
						$newcomments[$comment_id]['comment_date_gmt']	 = $comment['comment_date_gmt'];
						$newcomments[$comment_id]['comment_content']	  = $comment['comment_content'];
						$newcomments[$comment_id]['comment_approved']	 = $comment['comment_approved'];
						$newcomments[$comment_id]['comment_type']		 = $comment['comment_type'];
						$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
						$newcomments[$comment_id]['commentmeta']		  = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
						if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
							$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
					}
					ksort( $newcomments );

					foreach ( $newcomments as $key => $comment ) {
						// if this is a new post we can skip the comment_exists() check
						if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
							if ( isset( $inserted_comments[$comment['comment_parent']] ) )
								$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
							$comment = wp_filter_comment( $comment );
							$inserted_comments[$key] = wp_insert_comment( $comment );
							do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

							foreach( $comment['commentmeta'] as $meta ) {
								$value = maybe_unserialize( $meta['value'] );
								if(empty($value) && !empty($meta['value'])) {
									$value = maybe_unserialize( str_replace("\n", "\r\n", $meta['value']) );
								}
								add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
							}

							$num_comments++;
						}
					}
					unset( $newcomments, $inserted_comments, $post['comments'] );
				}

				if(!$exists_stop) {

					if ( ! isset( $post['postmeta'] ) )
						$post['postmeta'] = array();

						$elementor_data = false;
						foreach($post['postmeta'] as $v) {
							if ($v['key']=='_elementor_data') {
								$elementor_data = $v['value'];
							}
						}

						$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

						if ($elementor_data !== false) {
							foreach($post['postmeta'] as $k=>$v) {
								if ($v['key']=='_elementor_data') {
									$post['postmeta'][$k]['value'] = $elementor_data;
								}
							}
						}
		
					// add/update post meta
					if ( ! empty( $post['postmeta'] ) ) {
						foreach ( $post['postmeta'] as $meta ) {
							$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
							$value = false;

							if ( '_edit_last' == $key ) {
								if ( isset( $this->processed_authors[intval($meta['value'])] ) )
									$value = $this->processed_authors[intval($meta['value'])];
								else
									$key = false;
							}

							if ( $key ) {
								// export gets meta straight from the DB so could have a serialized string
								if (!$value) {
									$value = maybe_unserialize( $meta['value'] );
								}

								if(empty($value) && !empty($meta['value'])) {
									$value = maybe_unserialize( str_replace("\n", "\r\n", $meta['value']) );
								}

								switch ($key) {
									case '_elementor_data':
										if ($import_media) {
											$st=microtime(true);
											//file_put_contents('/var/www/html/wp-content/b-'.$post_id,$value);
											$lastPostId = $this->get_last_post_id();
											$value = thegem_importer_process_elementor_data($post_id, $value); 
											//file_put_contents('/var/www/html/wp-content/a-'.$post_id,$value);
											write_log("parse elementor_data for post $post_id in ".(microtime(true)-$st)."sec posts $lastPostId-".$this->get_last_post_id());
										}

										break;
									case '_elementor_page_settings':
										if ($import_media) {
											$st=microtime(true);
											$value = thegem_importer_process_elementor_page_settings($post_id, $value); 
											write_log("parse elementor_page_settings for post $post_id in ".(microtime(true)-$st)."sec");
										}
										break;
								}
								
								
								$value = $this->process_content_links($value);
								$key = wp_slash($key);
								$value = wp_slash($value);
								update_post_meta( $post_id, $key, $value );

								do_action( 'import_post_meta', $post_id, $key, $value );

								// if the post has a featured image, take note of this in case of remap
								if ( '_thumbnail_id' == $key )
									$this->featured_images[$post_id] = (int) $value;
							}
						}
					}
				}
			}

			write_log('<<< process_posts '.implode(',',$ids_array));
			//unset( $this->posts );
		}

		/**
		 * Attempt to create a new menu item from import data
		 *
		 * Fails for draft, orphaned menu items and those without an associated nav_menu
		 * or an invalid nav_menu term. If the post type or term object which the menu item
		 * represents doesn't exist then the menu item will not be imported (waits until the
		 * end of the import to retry again before discarding).
		 *
		 * @param array $item Menu item details from WXR file
		 */
		function process_menu_item( $item ) {
			// skip draft, orphaned menu items
			if ( 'draft' == $item['status'] )
				return;

			$menu_slug = false;
			if ( isset($item['terms']) ) {
				// loop through terms, assume first nav_menu term is correct menu
				foreach ( $item['terms'] as $term ) {
					if ( 'nav_menu' == $term['domain'] ) {
						$menu_slug = $term['slug'];
						break;
					}
				}
			}

			// no nav_menu term associated with this menu item
			if ( ! $menu_slug ) {
				_e( 'Menu item skipped due to missing menu slug', 'wordpress-importer' );
				echo '<br />';
				return;
			}

			$menu_id = term_exists( $menu_slug, 'nav_menu' );
			if ( ! $menu_id ) {
				printf( __( 'Menu item skipped due to invalid menu slug: %s', 'wordpress-importer' ), esc_html( $menu_slug ) );
				echo '<br />';
				return;
			} else {
				$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
			}

			foreach ( $item['postmeta'] as $meta ) {
				$meta_name = $meta['key'];
				$$meta_name = $this->process_content_links($meta['value']);
			}

			if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
				$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
			} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
				$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
			} else if ( 'custom' != $_menu_item_type ) {
				// associated object is missing or not imported yet, we'll retry later
				$this->missing_menu_items[] = $item;
				return;
			}

			if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
				$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
			} else if ( $_menu_item_menu_item_parent ) {
				$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
				$_menu_item_menu_item_parent = 0;
			}

			// wp_update_nav_menu_item expects CSS classes as a space separated string
			$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
			if ( is_array( $_menu_item_classes ) )
				$_menu_item_classes = implode( ' ', $_menu_item_classes );

			$args = array(
				'menu-item-object-id' => $_menu_item_object_id,
				'menu-item-object' => $_menu_item_object,
				'menu-item-parent-id' => $_menu_item_menu_item_parent,
				'menu-item-position' => intval( $item['menu_order'] ),
				'menu-item-type' => $_menu_item_type,
				'menu-item-title' => $item['post_title'],
				'menu-item-url' => $_menu_item_url,
				'menu-item-description' => $item['post_content'],
				'menu-item-attr-title' => $item['post_excerpt'],
				'menu-item-target' => $_menu_item_target,
				'menu-item-classes' => $_menu_item_classes,
				'menu-item-xfn' => $_menu_item_xfn,
				'menu-item-status' => $item['status']
			);

			$copy_meta_query = array(
				array(
					'key' => '_menu_item_type',
					'value' => $_menu_item_type
				),
				array(
					'key' => '_menu_item_object',
					'value' => $_menu_item_object
				)
			);
			if($_menu_item_type == 'taxonomy' || $_menu_item_type == 'post_type') {
				$copy_meta_query[] = array(
					'key' => '_menu_item_object_id',
					'value' => $_menu_item_object_id
				);
			} else {
				$copy_meta_query[] = array(
					'key' => '_menu_item_url',
					'value' => $_menu_item_url
				);
			}
			$check_copy = wp_get_nav_menu_items($menu_id, array(
				'meta_query' => $copy_meta_query
			));
			$is_copy = false;
			$id = 0;
			foreach($check_copy as $cc) {
				if(!empty($cc->post_title) && $cc->post_title == $item['post_title']) {
					$is_copy = true;
					$id = $cc->ID;
				}
			}

			if(!$is_copy) {
				$id = wp_update_nav_menu_item( $menu_id, 0, $args );
			}

			if ( $id && ! is_wp_error( $id ) )
				$this->processed_menu_items[intval($item['post_id'])] = (int) $id;
		}

		/**
		 * If fetching attachments is enabled then attempt to create a new attachment
		 *
		 * @param array $post Attachment post details from WXR
		 * @param string $url URL to fetch attachment from
		 * @return int|WP_Error Post ID on success, WP_Error otherwise
		 */
		function process_attachment( $post, $url ) {
			if ( ! $this->fetch_attachments )
				return new WP_Error( 'attachment_processing_error',
					__( 'Fetching attachments is not enabled', 'wordpress-importer' ) );

			// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
			if ( preg_match( '|^/[\w\W]+$|', $url ) )
				$url = rtrim( $this->base_url, '/' ) . $url;

			$time=microtime(true);
			
			$upload = $this->fetch_remote_file( $url, $post );

			if ( is_wp_error( $upload ) ) {
				write_log("failed to download url $url, error: ".$upload->get_error_message());
				return $upload;
			}

			$uSize=filesize($upload['file']);
			$uTime=microtime(true)-$time;
			write_log("downloaded $uSize bytes in ".number_format($uTime,3)." sec (".intval($uSize/$uTime/1024)."kb/s url ".$url);

			if ( $info = wp_check_filetype( $upload['file'] ) )
				$post['post_mime_type'] = $info['type'];
			else
				return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wordpress-importer') );

			$post['guid'] = $upload['url'];

			// as per wp-admin/includes/upload.php
			write_log("insert attachment");
			$post_id = wp_insert_attachment( $post, $upload['file'] );
			write_log("attachment inserted");
			$attachment_metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
			write_log("metadata generated");
			wp_update_attachment_metadata( $post_id, $attachment_metadata);
			write_log("updated attachment metadata");
			write_log("hash is ".sha1($url));
			update_post_meta( $post_id, '_elementor_source_image_hash', sha1($url) );
			// write_log('imported demo url ' . $url . ' with hash ' . sha1($url));

			// remap resized image URLs, works by stripping the extension and remapping the URL stub.
			if ( preg_match( '!^image/!', $info['type'] ) ) {
				$parts = pathinfo( $url );
				$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

				$parts_new = pathinfo( $upload['url'] );
				$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

				$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
			}

			return $post_id;
		}

		/**
		 * Attempt to download a remote file attachment
		 *
		 * @param string $url URL of item to fetch
		 * @param array $post Attachment details
		 * @return array|WP_Error Local file location details on success, WP_Error otherwise
		 */
		function fetch_remote_file( $url, $post ) {
			// extract the file name and extension from the url
			$file_name = basename( $url );

			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] )
				return new WP_Error( 'upload_dir_error', $upload['error'] );

			// fetch the remote url and write it to the placeholder file
			$remote_response = wp_safe_remote_get( $url, array(
				'timeout' => 300,
						'stream' => true,
						'filename' => $upload['file'],
				) );

			$headers = wp_remote_retrieve_headers( $remote_response );

			// request failed
			if ( ! $headers ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote server did not respond', 'wordpress-importer') );
			}

			$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

			// make sure the fetch was successful
			if ( $remote_response_code != '200' ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'wordpress-importer'), esc_html($remote_response_code), get_status_header_desc($remote_response_code) ) );
			}

			$filesize = filesize( $upload['file'] );

			/*if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'wordpress-importer') );
			}*/

			if ( 0 == $filesize ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'wordpress-importer') );
			}

			$max_size = (int) $this->max_attachment_size();
			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'wordpress-importer'), size_format($max_size) ) );
			}

			// keep track of the old and new urls so we can substitute them later
			$this->url_remap[$url] = $upload['url'];
			$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
			// keep track of the destination if the remote url is redirected somewhere else
			if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url ) {
				$this->url_remap[$headers['x-final-location']] = $upload['url'];
			}

			return $upload;
		}

		/**
		 * Attempt to associate posts and menu items with previously missing parents
		 *
		 * An imported post's parent may not have been imported when it was first created
		 * so try again. Similarly for child menu items and menu items which were missing
		 * the object (e.g. post) they represent in the menu
		 */
		function backfill_parents() {
			global $wpdb;

			// find parents for post orphans
			foreach ( $this->post_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = false;
				if ( isset( $this->processed_posts[$child_id] ) )
					$local_child_id = $this->processed_posts[$child_id];
				if ( isset( $this->processed_posts[$parent_id] ) )
					$local_parent_id = $this->processed_posts[$parent_id];

				if ( $local_child_id && $local_parent_id )
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
					write_log("set parent_id $local_parent_id for post id $local_child_id");
			}

			// all other posts/terms are imported, retry menu items with missing associated object
			$missing_menu_items = $this->missing_menu_items;
			foreach ( $missing_menu_items as $item )
				$this->process_menu_item( $item );

			// find parents for menu item orphans
			foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = 0;
				if ( isset( $this->processed_menu_items[$child_id] ) )
					$local_child_id = $this->processed_menu_items[$child_id];
				if ( isset( $this->processed_menu_items[$parent_id] ) )
					$local_parent_id = $this->processed_menu_items[$parent_id];

				if ( $local_child_id && $local_parent_id )
					update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
			}
		}

		/**
		 * Use stored mapping information to update old attachment URLs
		 */
		function backfill_attachment_urls() {
			global $wpdb;
			// make sure we do the longest urls first, in case one is a substring of another
			uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );

			update_option('thegem_import_attachment_urls',array_merge(get_option('thegem_import_attachment_urls',array()),$this->url_remap));

			foreach ( $this->url_remap as $from_url => $to_url ) {
				// remap urls in post_content
				$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
				// remap enclosure urls
				$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
			}
		}

		/**
		 * Update _thumbnail_id meta to new, imported attachment IDs
		 */
		function remap_featured_images() {
			// cycle through posts that have a featured image
			foreach ( $this->featured_images as $post_id => $value ) {
				if ( isset( $this->processed_posts[$value] ) ) {
					$new_id = $this->processed_posts[$value];
					// only update if there's a difference
					if ( $new_id != $value )
						update_post_meta( $post_id, '_thumbnail_id', $new_id );
				}
			}
		}

		/**
		 * Parse a WXR file
		 *
		 * @param string $file Path to WXR file for parsing
		 * @return array Information gathered from the WXR file
		 */
		function parse( $file ) {
			$parser = new WXR_Parser();
			return $parser->parse( $file );
		}

		// Display import page title
		function header() {
			echo '<div class="wrap">';
			screen_icon();
			echo '<h2>' . __( 'Import WordPress', 'wordpress-importer' ) . '</h2>';

			$updates = get_plugin_updates();
			$basename = plugin_basename(__FILE__);
			if ( isset( $updates[$basename] ) ) {
				$update = $updates[$basename];
				echo '<div class="error"><p><strong>';
				printf( __( 'A new version of this importer is available. Please update to version %s to ensure compatibility with newer export files.', 'wordpress-importer' ), $update->update->new_version );
				echo '</strong></p></div>';
			}
		}

		// Close div.wrap
		function footer() {
			echo '</div>';
		}

		/**
		 * Display introductory text and file upload form
		 */
		function greet() {
			echo '<div class="narrow">';
			echo '<p>'.__( 'Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts, pages, comments, custom fields, categories, and tags into this site.', 'wordpress-importer' ).'</p>';
			echo '<p>'.__( 'Choose a WXR (.xml) file to upload, then click Upload file and import.', 'wordpress-importer' ).'</p>';
			wp_import_upload_form( 'admin.php?import=wordpress&amp;step=1' );
			echo '</div>';
		}

		/**
		 * Decide if the given meta key maps to information we will want to import
		 *
		 * @param string $key The meta key to check
		 * @return string|bool The key if we do want to import, false if not
		 */
		function is_valid_meta_key( $key ) {
			// skip attachment metadata since we'll regenerate it from scratch
			// skip _edit_lock as not relevant for import
			if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
				return false;
			return $key;
		}

		/**
		 * Decide whether or not the importer is allowed to create users.
		 * Default is true, can be filtered via import_allow_create_users
		 *
		 * @return bool True if creating users is allowed
		 */
		function allow_create_users() {
			return apply_filters( 'import_allow_create_users', true );
		}

		/**
		 * Decide whether or not the importer should attempt to download attachment files.
		 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
		 * made at the import options screen must also be true, false here hides that checkbox.
		 *
		 * @return bool True if downloading attachments is allowed
		 */
		function allow_fetch_attachments() {
			return apply_filters( 'import_allow_fetch_attachments', true );
		}

		/**
		 * Decide what the maximum file size for downloaded attachments is.
		 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
		 *
		 * @return int Maximum attachment file size to import
		 */
		function max_attachment_size() {
			return apply_filters( 'import_attachment_size_limit', 0 );
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 * @return int 60
		 */
		function bump_request_timeout( $val ) {
			return 60;
		}

		// return the difference in length between two strings
		function cmpr_strlen( $a, $b ) {
			return strlen($b) - strlen($a);
		}

	}

} // class_exists( 'WP_Importer' )

function wordpress_importer_init() {
	load_plugin_textdomain( 'wordpress-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * WordPress Importer object for registering the import callback
	 * @global WP_Import $wp_import
	 */
	$GLOBALS['wp_import'] = new WP_Import();
	register_importer( 'wordpress', 'WordPress', __('Import <strong>posts, pages, comments, custom fields, categories, and tags</strong> from a WordPress export file.', 'wordpress-importer'), array( $GLOBALS['wp_import'], 'dispatch' ) );
}
add_action( 'admin_init', 'wordpress_importer_init' );
