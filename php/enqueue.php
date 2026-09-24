<?php

namespace TSJIPPY\STATISTICS;

use TSJIPPY;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\loadAssets');
/**
 * Registeres the CSS and JS
 */
function loadAssets()
{
    //Load js
    wp_enqueue_script_module('@tsjippy/statistics_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/statistics' . TSJIPPY\JSEXTENSION), array("@tsjippy/nonce_script"), PLUGINVERSION);
}
