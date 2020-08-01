<?php

/**
 * Plugin Name: Barkah Tools
 * Plugin URI: http://barkah.id
 * Description: A plugin demonstrating how to add a new WooCommerce integration.
 * Author:  husen efendi
 * Author URI: http://barkah.id
 * Version: 1.0
 */
if (!class_exists('WC_Barkah_tools')) :
    class WC_Barkah_tools
    {
        /**
         * Construct the plugin.
         */
        public function __construct()
        {
            add_action('plugins_loaded', array($this, 'init'));
        }
        /**
         * Initialize the plugin.
         */
        public function init()
        {
            // Checks if WooCommerce is installed.
            if (class_exists('WC_Integration')) {
                // Include our integration class.
                include_once 'barkah-tools-integration.php';
                include_once 'includes/wa-button.php';
                // Register the integration.
                add_filter('woocommerce_integrations', array($this, 'add_integration'));
            }
        }
        /**
         * Add a new integration to WooCommerce.
         */
        public function add_integration($integrations)
        {
            $integrations[] = 'WC_Barkah_Tools_Integration';
            return $integrations;
        }
    }
    $WC_Barkah_tools = new WC_Barkah_tools(__FILE__);
endif;
// Set the plugin slug
define('BT_PLUGIN_SLUG', 'wc-settings');
define('BT_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));

// Setting action for plugin
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'WC_Barkah_tools_action_links');
function WC_Barkah_tools_action_links($links)
{
    $links[] = '<a href="' . menu_page_url(BT_PLUGIN_SLUG, false) . '&tab=integration">Settings</a>';
    return $links;
}
