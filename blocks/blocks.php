<?php

namespace TSJIPPY\STATISTICS;

use TSJIPPY;

add_action('init', __NAMESPACE__ . '\blockInit');
function blockInit()
{
    register_block_type(
        'tsjippy-statistics/show',
        array(
            'title'           => __( 'Post Statistics', '%TEXTDOMAIN%' ),
            'render_callback' => __NAMESPACE__.'\postStatistics',
            'supports'        => array(
                'autoRegister' => true,
            ),
            'icon'  => 'plus'
        )
    );
}

function postStatistics()
{
    $viewRoles     = SETTINGS['view-rights'] ?? [];
    $userRoles     = wp_get_current_user()->roles;

    //only continue if we have the right so see the statistics
    if (!array_intersect($viewRoles, $userRoles)) {
        return '';
    }

    global $wpdb;

    $tableName  = $wpdb->prefix . 'tsjippy_statistics';
    $url        = str_replace(TSJIPPY\SITEURL, '', TSJIPPY\currentUrl());

    $pageViews  = $wpdb->get_results("SELECT * FROM $tableName WHERE url='$url' ORDER BY $tableName.`time_last_edited` DESC");

    $totalViews             = 0;
    $uniqueViewsLastMonths  = 0;
    $now                    = new \DateTime();
    foreach ($pageViews as $view) {
        $totalViews += $view->counter;

        $date = new \DateTime($view->time_last_edited);
        $interval = $now->diff($date)->format('%m months');
        if ($interval < 6) {
            $uniqueViewsLastMonths++;
        }
    }
    $uniqueViews   = count($pageViews);

    ob_start();
    ?>
    <div class='pagestatistics'>
        <h4>
            Page statistics
        </h4>
        <table class='statistics-table'>
            <tbody>
                <tr>
                    <td>
                        <strong>Total views:</strong>
                    </td>
                    <td class='value'>
                        <?php echo esc_attr($totalViews); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Unique views:</strong>
                    </td>
                    <td class='value'>
                        <?php echo esc_attr($uniqueViews); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Unique views last 6 months:</strong>
                    </td>
                    <td class='value'>
                        <?php echo esc_attr($uniqueViewsLastMonths); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php

    return ob_get_clean();
}