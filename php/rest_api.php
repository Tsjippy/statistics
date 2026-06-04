<?php

namespace TSJIPPY\STATISTICS;

use TSJIPPY;

// Allow rest api urls for non-logged in users
add_filter('tsjippy_allowed_rest_api_urls', __NAMESPACE__ . '\restApiUrls');
/**
 * Adds the statistics URLs to the list of allowed REST API URLs.
 *
 * @param array $urls The list of allowed REST API URLs.
 * @return array The updated list of allowed REST API URLs.
 */
function restApiUrls($urls)
{
    $urls[]    = RESTAPIPREFIX . '/statistics/add_page_view';

    return $urls;
}

add_action('rest_api_init',  __NAMESPACE__ . '\restApiInit');
function restApiInit()
{
    // Check for existing travel request
    register_rest_route(
        RESTAPIPREFIX . '/statistics',
        '/add_page_view',
        array(
            'methods'                 => \WP_REST_Server::CREATABLE,
            'callback'                 => function () {
                $statistics    = new Statistics();
                $statistics->addPageView();
            },
            'permission_callback'     => '__return_true',        // Allow non-logged in users to access this endpoint
            'args'                    => array(
                'url'        => array('required'    => true),
            )
        )
    );
}
