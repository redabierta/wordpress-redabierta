<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.sharedfilespro.com
 * @since             1.0.0
 * @package           Shared_Files
 *
 * @wordpress-plugin
 * Plugin Name: Shared Files (Premium)
 * Description:       A simple yet effective tool to list downloadable files on your site.
 * Version:           1.6.23
 * Author:            Tammersoft
 * Author URI:        https://www.tammersoft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shared-files
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SHARED_FILES_VERSION', '1.6.23' );
define( 'SHARED_FILES_URI', plugin_dir_url( __FILE__ ) );
define( 'SHARED_FILES_PATH', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shared-files-activator.php
 */
function activate_shared_files()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-shared-files-activator.php';
    Shared_Files_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shared-files-deactivator.php
 */
function deactivate_shared_files()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-shared-files-deactivator.php';
    Shared_Files_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shared_files' );
register_deactivation_hook( __FILE__, 'deactivate_shared_files' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-shared-files.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shared_files()
{
    $plugin = new Shared_Files();
    $plugin->run();
}

run_shared_files();
