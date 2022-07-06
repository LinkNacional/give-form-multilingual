<?php

/**
 * Form Multilingual for GiveWP Frontend Actions
 *
 * @since 1.0.0
 *
 * @copyright  Copyright (c) 2022, Link Nacional
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 */

// Exit, if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set locale input for shortcode give forms
 *
 * @param  string $form_id
 * @param  array $args
 *
 * @return void
 */
function lkn_give_form_multilingual_set_locale($form_id, $args) {
    ?>
    <input type="hidden" name="lkn_give_form_multilingual_locale" value="<?php esc_attr_e(pll_current_language()); ?>">
    <?php
}

add_action('give_donation_form_top', 'lkn_give_form_multilingual_set_locale', 10, 3);

/**
 * Modify success page URL to translated URL
 *
 * @return void
 */
function lkn_give_form_multilingual_get_success_page_uri_with_language() {
    $give_options = give_get_settings();
    $page_id = $give_options['success_page'];
    $formLang = sanitize_text_field($_POST['lkn_give_form_multilingual_locale']);

    if (!empty($formLang)) {
        $page_id = pll_get_post($page_id, $formLang);
    } elseif (function_exists('pll_get_post')) {
        $page_id = pll_get_post($page_id);
    }

    $success_page = isset($page_id) ? get_permalink(absint($page_id)) : get_bloginfo('url');

    return $success_page;
}

add_filter('give_get_success_page_uri', 'lkn_give_form_multilingual_get_success_page_uri_with_language', 10, 1);

/**
 * Modify failed page URL to translated URL
 *
 * @return void
 */
function lkn_give_form_multilingual_get_failed_page_uri_with_language() {
    $give_options = give_get_settings();
    $page_id = $give_options['failure_page'];
    $formLang = sanitize_text_field($_POST['lkn_give_form_multilingual_locale']);

    if (!empty($formLang)) {
        $page_id = pll_get_post($page_id, $formLang);
    } elseif (function_exists('pll_get_post')) {
        $page_id = pll_get_post($page_id);
    }

    $success_page = isset($page_id) ? get_permalink(absint($page_id)) : get_bloginfo('url');

    return $success_page;
}

add_filter('give_get_failed_transaction_uri', 'lkn_give_form_multilingual_get_failed_page_uri_with_language', 10, 1);

/**
 * Modify history page URL to translated URL
 *
 * @return void
 */
function lkn_give_form_multilingual_get_donation_history_page_uri_with_language() {
    $give_options = give_get_settings();
    $page_id = $give_options['history_page'];
    $formLang = sanitize_text_field($_POST['lkn_give_form_multilingual_locale']);

    if (!empty($formLang)) {
        $page_id = pll_get_post($page_id, $formLang);
    } elseif (function_exists('pll_get_post')) {
        $page_id = pll_get_post($page_id);
    }

    $success_page = isset($page_id) ? get_permalink(absint($page_id)) : get_bloginfo('url');

    return $success_page;
}

add_filter('give_get_history_page_uri', 'lkn_give_form_multilingual_get_donation_history_page_uri_with_language', 10, 1);
