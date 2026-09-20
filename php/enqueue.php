<?php

namespace TSJIPPY\STATISTICS;

use TSJIPPY;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\loadAssets');
function loadAssets()
{
    //Load js
    wp_enqueue_script_module('@tsjippy/statistics_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/statistics' . TSJIPPY\JSEXTENSION), array(), PLUGINVERSION);

    add_filter( 'script_module_data_@tsjippy/statistics_script', function($data){
        $data['baseUrl']       = get_home_url();
        $data['restApiPrefix'] = '/' . TSJIPPY\RESTAPIPREFIX;
        $data['restNonce']     = wp_create_nonce('wp_rest');

        return $data; 
    } );
}
