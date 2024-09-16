<?php
/**
 * Plugin Name: Malwarebytes Strings Translator (Technical Challenge)
 * Plugin URI: https://github.com/dneuro/mb-test
 * Description: A plugin to translate strings into multiple languages using by Weglot API
 * Version: 1.0.0
 * Author: Dan
 */

if (!defined( 'ABSPATH')) {
    exit;
}
require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/cron.php';
require_once __DIR__ . '/includes/translator.php';

$translator = new MBTranslator();
$translatorAdmin = new MBTranslatorAdmin();
$translatorApi = new MBTranslatorApi();
$translatorCron = new MBTranslatorCron();

register_activation_hook(__FILE__, function () use ($translator) {
    verify_weglot_is_active();
    $translator->create_translations_table();
});

register_deactivation_hook(__FILE__, [$translator, 'drop_translations_table']);

function verify_weglot_is_active() {
    if (!is_plugin_active('weglot/weglot.php')) {
        deactivate_plugins(plugin_basename(__FILE__));

        wp_die(
            'Weglot Strings Translator requires Weglot to be installed and activated before activation.',
            'Weglot Strings Translator Activation Error',
            ['back_link' => true]
        );
    }
}

add_action('init', [$translatorAdmin, 'init']);
add_action('init', [$translatorApi, 'init']);
add_action('init', [$translatorCron, 'init']);