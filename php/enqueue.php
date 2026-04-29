<?php
namespace TSJIPPY\STATISTICS;
use TSJIPPY;

add_action('wp_enqueue_scripts', __NAMESPACE__.'\loadAssets' );
function loadAssets(){
    //Load js
    wp_enqueue_script('tsjippy_statistics_script', TSJIPPY\pathToUrl(PLUGINPATH.'js/statistics.min.js'), array(), PLUGINVERSION,true);
}