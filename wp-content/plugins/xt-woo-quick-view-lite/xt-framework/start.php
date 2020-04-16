<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package     XT_Framework
 * @copyright   Copyright (c) 2019, XT_Framework, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

/**
 * XT Plugin Framework Version.
 *
 * @var string
 */
$this_fw_version = '1.1.5';

/**
 * Special logic to make sure that every XT plugin framework
 * will ALWAYS be loaded with the newest version from the active plugins.
 *
 * @since 1.0.0
 */

global $xtfw_active_plugins;

if ( ! function_exists( 'xtfw_find_caller_plugin_file' ) ) {
	// Require SDK essentials.
	require_once dirname( __FILE__ ) . '/includes/functions-essential.php';
}


$file_path      = xtfw_normalize_path( __FILE__ );
$xtfw_root_path = dirname( $file_path );

$this_fw_relative_path = plugin_basename( $xtfw_root_path );

if ( ! isset( $xtfw_active_plugins ) ) {
	// Load all active XT Framework powered plugins.
	$xtfw_active_plugins = get_option( 'xtfw_active_plugins', new stdClass() );

	if ( ! isset( $xtfw_active_plugins->plugins ) ) {
		$xtfw_active_plugins->plugins = array();
	}
}

if ( empty( $xtfw_active_plugins->abspath ) ) {
	/**
	 * Store the WP install absolute path reference to identify environment change
	 * while replicating the storage.
	 */
	$xtfw_active_plugins->abspath = ABSPATH;
} else {
	if ( ABSPATH !== $xtfw_active_plugins->abspath ) {
		/**
		 * WordPress path has changed, cleanup the SDK references cache.
		 * This resolves issues triggered when spinning a staging environments
		 * while replicating the database.
		 */
		$xtfw_active_plugins->abspath = ABSPATH;
		$xtfw_active_plugins->plugins = array();
		unset( $xtfw_active_plugins->newest );
	} else {
		/**
		 * Make sure SDK references are still valid. This resolves
		 * issues when users hard delete modules via FTP.
		 */
		$has_changes = false;
		foreach ( $xtfw_active_plugins->plugins as $fw_path => $data ) {
			if ( ! file_exists( WP_PLUGIN_DIR . '/' . $fw_path ) ) {
				unset( $xtfw_active_plugins->plugins[ $fw_path ] );
				$has_changes = true;
			}
		}

		if ( $has_changes ) {
			if ( empty( $xtfw_active_plugins->plugins ) ) {
				unset( $xtfw_active_plugins->newest );
			}

			update_option( 'xtfw_active_plugins', $xtfw_active_plugins );
		}
	}
}


// Update current SDK info based on the SDK path.
if ( ! isset( $xtfw_active_plugins->plugins[ $this_fw_relative_path ] ) ||
     $this_fw_version != $xtfw_active_plugins->plugins[ $this_fw_relative_path ]->version
) {

	$plugin_path = plugin_basename( xtfw_find_direct_caller_plugin_file( $file_path ) );

	$xtfw_active_plugins->plugins[ $this_fw_relative_path ] = (object) array(
		'version'     => $this_fw_version,
		'timestamp'   => time(),
		'plugin_path' => $plugin_path,
	);
}

$is_current_fw_newest = isset( $xtfw_active_plugins->newest ) && ( $this_fw_relative_path == $xtfw_active_plugins->newest->fw_path );

if ( ! isset( $xtfw_active_plugins->newest ) ) {

	/**
	 * This will be executed only once, for the first time a XT_Framework powered plugin is activated.
	 */
	xtfw_update_fw_newest_version( $this_fw_relative_path, $xtfw_active_plugins->plugins[ $this_fw_relative_path ]->plugin_path );

	$is_current_fw_newest = true;

} else if ( version_compare( $xtfw_active_plugins->newest->version, $this_fw_version, '<' ) ) {

	/**
	 * Current SDK is newer than the newest stored SDK.
	 */
	xtfw_update_fw_newest_version( $this_fw_relative_path, $xtfw_active_plugins->plugins[ $this_fw_relative_path ]->plugin_path );

	if ( class_exists( 'XT_Framework' ) ) {
		// Older SDK version was already loaded.

		if ( ! $xtfw_active_plugins->newest->in_activation ) {
			// Re-order plugins to load this plugin first.
			xtfw_newest_fw_plugin_first();
		}

		// Refresh page.
		xtfw_redirect( $_SERVER['REQUEST_URI'] );
	}

} else {

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$fw_starter_path = xtfw_normalize_path( WP_PLUGIN_DIR . '/' . $this_fw_relative_path . '/start.php' );

	$is_newest_fw_path_valid = ( $xtfw_active_plugins->newest->in_activation ) && file_exists( $fw_starter_path );

	if ( ! $is_newest_fw_path_valid && ! $is_current_fw_newest ) {
		// Plugin with newest SDK is no longer active, or SDK was moved to a different location.
		unset( $xtfw_active_plugins->plugins[ $xtfw_active_plugins->newest->fw_path ] );
	}

	if ( ! ( $xtfw_active_plugins->newest->in_activation ) ||
	     ! $is_newest_fw_path_valid ||
	     // Is newest SDK downgraded.
	     ( $this_fw_relative_path == $xtfw_active_plugins->newest->fw_path &&
	       version_compare( $xtfw_active_plugins->newest->version, $this_fw_version, '>' ) )
	) {
		/**
		 * Plugin with newest SDK is no longer active.
		 *    OR
		 * The newest SDK was in the current plugin. BUT, seems like the version of
		 * the SDK was downgraded to a lower SDK.
		 */
		// Find the active plugin with the newest SDK version and update the newest reference.
		xtfw_fallback_to_newest_active_fw();
	} else {
		if ( $this_fw_relative_path == $xtfw_active_plugins->newest->fw_path &&
		     ( $xtfw_active_plugins->newest->in_activation ||
		       ( class_exists( 'XT_Framework' ) && ( ! defined( 'XTFW_VERSION' ) || version_compare( XTFW_VERSION, $this_fw_version, '<' ) ) )
		     )

		) {
			if ( $xtfw_active_plugins->newest->in_activation ) {
				// Plugin no more in activation.
				$xtfw_active_plugins->newest->in_activation = false;
				update_option( 'xtfw_active_plugins', $xtfw_active_plugins );
			}

			// Reorder plugins to load plugin with newest SDK first.
			if ( xtfw_newest_fw_plugin_first() ) {
				// Refresh page after re-order to make sure activated plugin loads newest SDK.
				if ( class_exists( 'XT_Framework' ) ) {
					xtfw_redirect( $_SERVER['REQUEST_URI'] );
				}
			}
		}
	}
}

// If SDK not already loaded, Load It.
if ( ! class_exists( 'XT_Framework' ) ) {

	if ( ! defined( 'XTFW_VERSION' ) ) {
		define( 'XTFW_VERSION', $this_fw_version );
	}

	$plugins_dir_path = xtfw_normalize_path( trailingslashit( WP_PLUGIN_DIR ) );

	if ( 0 === strpos( $file_path, $plugins_dir_path ) ) {
		// No symlinks
	} else {
		/**
		 * This logic finds the SDK symlink and set XTFW_DIR to use it.
		 */
		$fw_symlink = null;

		// Try to load SDK's symlink from cache.
		if ( isset( $xtfw_active_plugins->plugins[ $this_fw_relative_path ] ) &&
		     is_object( $xtfw_active_plugins->plugins[ $this_fw_relative_path ] ) &&
		     ! empty( $xtfw_active_plugins->plugins[ $this_fw_relative_path ]->fw_symlink )
		) {
			$fw_symlink = $xtfw_active_plugins->plugins[ $this_fw_relative_path ]->fw_symlink;
			if ( 0 === strpos( $fw_symlink, $plugins_dir_path ) ) {
				/**
				 * Make the symlink path relative.
				 */
				$fw_symlink = substr( $fw_symlink, strlen( $plugins_dir_path ) );

				$xtfw_active_plugins->plugins[ $this_fw_relative_path ]->fw_symlink = $fw_symlink;
				update_option( 'xtfw_active_plugins', $xtfw_active_plugins );
			}

			$realpath = realpath( $plugins_dir_path . $fw_symlink );
			if ( ! is_string( $realpath ) || ! file_exists( $realpath ) ) {
				$fw_symlink = null;
			}
		}

		if ( empty( $fw_symlink ) ) // Has symlinks, therefore, we need to configure XTFW_DIR based on the symlink.
		{
			$partial_path_right = basename( $file_path );
			$partial_path_left  = dirname( $file_path );
			$realpath           = realpath( $plugins_dir_path . $partial_path_right );

			while ( '/' !== $partial_path_left &&
			        ( false === $realpath || $file_path !== xtfw_normalize_path( $realpath ) )
			) {
				$partial_path_right     = trailingslashit( basename( $partial_path_left ) ) . $partial_path_right;
				$partial_path_left_prev = $partial_path_left;
				$partial_path_left      = dirname( $partial_path_left_prev );

				/**
				 * Avoid infinite loop if for example `$partial_path_left_prev` is `C:/`, in this case,
				 * `dirname( 'C:/' )` will return `C:/`.
				 */
				if ( $partial_path_left === $partial_path_left_prev ) {
					$partial_path_left = '';
					break;
				}

				$realpath = realpath( $plugins_dir_path . $partial_path_right );
			}

			if ( ! empty( $partial_path_left ) && '/' !== $partial_path_left ) {
				$fw_symlink = xtfw_normalize_path( dirname( $partial_path_right ) );

				// Cache value.
				if ( isset( $xtfw_active_plugins->plugins[ $this_fw_relative_path ] ) &&
				     is_object( $xtfw_active_plugins->plugins[ $this_fw_relative_path ] )
				) {
					$xtfw_active_plugins->plugins[ $this_fw_relative_path ]->fw_symlink = $fw_symlink;
					update_option( 'xtfw_active_plugins', $xtfw_active_plugins );
				}
			}
		}

		if ( ! empty( $fw_symlink ) ) {
			// Set SDK dir to the symlink path.
			define( 'XTFW_DIR', $plugins_dir_path . $fw_symlink );
		}
	}

	// Load SDK files.
	require_once dirname( __FILE__ ) . '/require.php';

}