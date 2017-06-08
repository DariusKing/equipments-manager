<?php

/**
 * @author            Dhanendran (https://dhanendranblog.wordpress.com/)
 * @link              https://dhanendranblog.wordpress.com/
 * @since             1.0.0
 * @package           Equipements_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Equipments Manager
 * Plugin URI:        https://github.com/dhanendran/equipments-manager
 * Description:       A WordPress plugin which will help you to manage equipments or physical assets in an organization.
 * Version:           1.0.0
 * Author:            Dhanendran
 * Author URI:        https://dhanendranblog.wordpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       equipments-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Include admin related settings and CPT creation.
require_once 'includes/admin-settings.php';
require_once 'includes/assignment.php';

$admin      = new EquipmentsManagerAdminSettings();
$assignment = new EquipmentsManagerAssignment();

/**
 * Add custom CSS files.
 */
function equipments_manager_add_css() {
	wp_enqueue_style( 'equipments_manager_css', plugin_dir_url( __FILE__ ) . 'css/style.css' );
}
add_action( 'init', 'equipments_manager_add_css' );
