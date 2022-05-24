<?php
/**
 * Plugin Name: Give - Form Multilingual
 * Plugin URI:  https://www.linknacional.com.br/wordpress/givewp/
 * Description: Add snipets to make Polylang compatible with GiveWP receipts.
 * Version:     1.0.0
 * Author:      Link Nacional
 * Author URI:  https://www.linknacional.com.br
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: give-form-multilingual
 */

require_once __DIR__ . '/plugin-updater/plugin-update-checker.php';

// Exit if accessed directly. ABSPATH is attribute in wp-admin - plugin.php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Lkn_Give_Form_Multilingual
 */
final class Lkn_Give_Form_Multilingual {
    /**
     * Instance.
     *
     * @since
     * @access private
     * @var Lkn_Give_Form_Multilingual
     */
    private static $instance;

    /**
     * Give - Tranlate Form Admin Object.
     *
     * @since  1.0.0
     * @access public
     *
     * @var    Lkn_Give_Form_Multilingual_Admin object.
     */
    public $plugin_admin;

    /**
     * Give - Tranlate Form Frontend Object.
     *
     * @since  1.0.0
     * @access public
     *
     * @var    Lkn_Give_Form_Multilingual_Frontend object.
     */
    public $plugin_public;

    /**
     * Singleton pattern.
     *
     * @since
     * @access private
     */
    private function __construct() {
        $this->load_plugin_textdomain();
        self::$instance = $this;
    }

    /**
     * Get instance.
     *
     * @return Lkn_Give_Form_Multilingual
     * @since
     * @access public
     *
     */
    public static function get_instance() {
        if (!isset(self::$instance) && !(self::$instance instanceof Lkn_Give_Form_Multilingual)) {
            self::$instance = new Lkn_Give_Form_Multilingual();
            self::$instance->setup();
        }

        return self::$instance;
    }

    /**
     * Setup
     *
     * @since
     * @access private
     */
    private function setup() {
        self::$instance->setup_constants();

        register_activation_hook(LKN_GIVE_FORM_MULTILINGUAL_FILE, [$this, 'install']);
        add_action('give_init', [$this, 'init'], 10, 1);
        add_action('plugins_loaded', [$this, 'check_environment'], 999);
    }

    /**
     * Setup constants
     *
     * Defines useful constants to use throughout the add-on.
     *
     * @since
     * @access private
     */
    private function setup_constants() {
        // Defines addon version number for easy reference.
        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_VERSION')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_VERSION', '1.0.0');
        }

        // Set it to latest.
        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_MIN_GIVE_VERSION')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_MIN_GIVE_VERSION', '2.19.2');
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_FILE')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_FILE', __FILE__);
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_SLUG')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_SLUG', 'give-form-multilingual');
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_DIR')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_DIR', plugin_dir_path(LKN_GIVE_FORM_MULTILINGUAL_FILE));
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_URL')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_URL', plugin_dir_url(LKN_GIVE_FORM_MULTILINGUAL_FILE));
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_BASENAME')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_BASENAME', plugin_basename(LKN_GIVE_FORM_MULTILINGUAL_FILE));
        }

        if (!defined('LKN_GIVE_FORM_MULTILINGUAL_TRANSLATION_PATH')) {
            define('LKN_GIVE_FORM_MULTILINGUAL_TRANSLATION_PATH', plugin_dir_path(__FILE__) . 'languages/');
        }
    }

    /**
     * Plugin installation
     *
     * @since
     * @access public
     */
    public function install() {
        // Bailout.
        if (!self::$instance->check_environment()) {
            return;
        }
    }

    /**
     * Plugin installation
     *
     * @param Give $give
     *
     * @return void
     * @since
     * @access public
     *
     */
    public function init($give) {
        if (!self::$instance->check_environment()) {
            //se não esta logado entra daqui
            self::$instance->load_files();
            self::$instance->setup_hooks();

            return;
        }

        self::$instance->load_files();
        self::$instance->setup_hooks();
    }

    /**
     * Check plugin environment
     *
     * @return bool|null
     * @since
     * @access public
     *
     */
    public function check_environment() {
        // Is not admin
        if (!is_admin() || !current_user_can('activate_plugins')) {
            require_once LKN_GIVE_FORM_MULTILINGUAL_DIR . 'includes/actions.php';

            return null;
        }

        // Load plugin helper functions.
        if (!function_exists('deactivate_plugins') || !function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        // Load helper functions.
        require_once LKN_GIVE_FORM_MULTILINGUAL_DIR . 'includes/misc-functions.php';

        // Flag to check whether deactivate plugin or not.
        $is_deactivate_plugin = false;

        // Verify dependency cases.
        switch (true) {
            case doing_action('give_init'):
                if (
                    defined('GIVE_VERSION') &&
                    version_compare(GIVE_VERSION, LKN_GIVE_FORM_MULTILINGUAL_MIN_GIVE_VERSION, '<')
                ) {
                    /* Min. Give. plugin version. */

                    // Show admin notice.
                    add_action('admin_notices', 'lkn_give_form_multilingual_dependency_notice');

                    $is_deactivate_plugin = true;
                }

                break;

            case doing_action('activate_' . LKN_GIVE_FORM_MULTILINGUAL_BASENAME):
            case doing_action('plugins_loaded') && !did_action('give_init'):
                /* Check to see if Give is activated, if it isn't deactivate and show a banner. */

                // Check for if give plugin activate or not.
                $is_give_active = defined('GIVE_PLUGIN_BASENAME') ? is_plugin_active(GIVE_PLUGIN_BASENAME) : false;

                if (!$is_give_active) {
                    add_action('admin_notices', 'lkn_give_form_multilingual_inactive_notice');

                    $is_deactivate_plugin = true;
                }

                $is_polylang_active = defined('POLYLANG_BASENAME') ? is_plugin_active(POLYLANG_BASENAME) : false;

                if (!$is_polylang_active) {
                    add_action('admin_notices', 'lkn_give_form_multilingual_polylang_inactive_notice');

                    $is_deactivate_plugin = true;
                }

                break;
        }

        // Don't let this plugin activate.
        if ($is_deactivate_plugin) {
            // Deactivate plugin.
            deactivate_plugins(LKN_GIVE_FORM_MULTILINGUAL_BASENAME);

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            return false;
        }

        return true;
    }

    /**
     * Load plugin files.
     *
     * @since
     * @access private
     */
    private function load_files() {
        require_once LKN_GIVE_FORM_MULTILINGUAL_DIR . 'includes/misc-functions.php';
    }

    /**
     * Setup hooks
     *
     * @since
     * @access private
     */
    private function setup_hooks() {
        // Filters
        add_filter('plugin_action_links_' . LKN_GIVE_FORM_MULTILINGUAL_BASENAME, 'lkn_give_form_multilingual_plugin_row_meta', 10, 2);
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    private function load_plugin_textdomain() {
        load_plugin_textdomain(
            'give-form-multilingual',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}

/**
 * The main function responsible for returning the one true Lkn_Give_Form_Multilingual instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $recurring = Lkn_Give_Form_Multilingual(); ?>
 *
 * @return Lkn_Give_Form_Multilingual|bool
 * @since 1.0
 *
 */
function lkn_give_form_multilingual() {
    return Lkn_Give_Form_Multilingual::get_instance();
}

lkn_give_form_multilingual();

/**
 * Instance of update checker
 *
 * @return object
 */
function lkn_give_form_multilingual_updater() {
    return new Lkn_Puc_Plugin_UpdateChecker(
        'https://api.linknacional.com.br/v2/u/?slug=give-form-multilingual&wp=1',
        __FILE__,
        'give-form-multilingual'
    );
}

lkn_give_form_multilingual_updater();
