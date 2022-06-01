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
 * Modify success page URL to translated URL
 *
 * @return void
 */
function lkn_give_form_multilingual_get_success_page_uri_with_language() {
    $give_options = give_get_settings();
    $page_id = $give_options['success_page'];

    if (function_exists('pll_get_post')) {
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

    if (function_exists('pll_get_post')) {
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

    if (function_exists('pll_get_post')) {
        $page_id = pll_get_post($page_id);
    }

    $success_page = isset($page_id) ? get_permalink(absint($page_id)) : get_bloginfo('url');

    return $success_page;
}

add_filter('give_get_history_page_uri', 'lkn_give_form_multilingual_get_donation_history_page_uri_with_language', 10, 1);
