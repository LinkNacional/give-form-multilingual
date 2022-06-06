<?php

// Exit, if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Show plugin dependency notice
 *
 * @since
 */
function lkn_give_form_multilingual_dependency_notice() {
    // Admin notice.
    $message = sprintf(
        '<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.',
        __('Activation Error:', 'give'),
        __('You must have', 'give'),
        'https://givewp.com',
        __('Give', 'give'),
        __('version', 'give'),
        LKN_GIVE_FORM_MULTILINGUAL_MIN_GIVE_VERSION,
        __('for the Form Multilingual for Give to activate', 'give')
    );

    Give()->notices->register_notice([
        'id' => 'give-activation-error',
        'type' => 'error',
        'description' => $message,
        'show' => true,
    ]);
}

/**
 * Notice for No Core Activation
 *
 * @since
 */
function lkn_give_form_multilingual_inactive_notice() {
    $allowed_html = [
        'a'      => [
            'href'  => [],
            'target' => [],
        ],
        'div'     => [
            'class' => [],
        ],
        'p'     => [],
        'strong' => [],
    ];

    // Admin notice.
    $message = sprintf(
        '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
        __('Activation Error:', 'give'),
        __('You must have', 'give'),
        'https://givewp.com',
        __('Give', 'give'),
        __(' plugin installed and activated for the Form Multilingual for Give', 'give')
    );

    echo wp_kses($message, $allowed_html);
}

/**
 * Notice for No Polylang Core Activation
 *
 * @since
 */
function lkn_give_form_multilingual_polylang_inactive_notice() {
    // Admin notice.
    $message = sprintf(
        '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
        __('Activation Error:', 'give'),
        __('You must have', 'give'),
        'https://wordpress.org/plugins/polylang/',
        __('Polylang', 'give'),
        __(' plugin installed and activated for the Form Multilingual for Give', 'give')
    );

    $allowed_html = [
        'a'      => [
            'href'  => [],
            'target' => [],
        ],
        'div'     => [
            'class' => [],
        ],
        'p'     => [],
        'strong' => [],
    ];

    echo wp_kses($message, $allowed_html);
}

/**
 * Plugin row meta links.
 *
 * @since
 *
 * @param array $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 *
 * @return array
*/
function lkn_give_form_multilingual_plugin_row_meta($plugin_meta, $plugin_file) {
    $new_meta_links['setting'] = sprintf(
        '<a href="%1$s">%2$s</a>',
        admin_url('edit.php?post_type=give_forms&page=give-forms'),
        __('Settings', 'give')
    );

    return array_merge($plugin_meta, $new_meta_links);
}

/**
 * Show activation banner
 *
 * @since
 * @return void
*/
function lkn_give_form_multilingual_activation() {
    // Initialize activation welcome banner.
    if (class_exists('Lkn_Give_Form_Multilingual')) {
        // Only runs on admin.
        $args = [
            'file' => LKN_GIVE_FORM_MULTILINGUAL_FILE,
            'name' => __('Form Multilingual for Give', 'give'),
            'version' => LKN_GIVE_FORM_MULTILINGUAL_MIN_GIVE_VERSION,
            'documentation_url' => 'https://www.linknacional.com.br/wordpress/givewp/',
            'support_url' => 'https://www.linknacional.com.br/suporte/',
            'testing' => false, // Never leave true.
        ];

        new Lkn_Give_Form_Multilingual($args);
    }
}
