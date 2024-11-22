<?php
namespace SIM\STATISTICS;
use SIM;

add_action('wp_enqueue_scripts', __NAMESPACE__.'\loadAssets' );
function loadAssets(){
    //Load js
    wp_enqueue_script('sim_statistics_script', SIM\pathToUrl(MODULE_PATH.'js/statistics.min.js'), array(), MODULE_VERSION,true);
}