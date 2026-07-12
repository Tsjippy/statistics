<?php

namespace TSJIPPY\STATISTICS;

use TSJIPPY;

use function TSJIPPY\addElement;
use function TSJIPPY\addRawHtml;

if (! defined('ABSPATH')) {
    exit;
}

class AdminMenu extends TSJIPPY\ADMIN\SubAdminMenu
{

    /**
     * AdminMenu constructor.
     *
     * @param array $settings The settings for the plugin
     * @param string $name The name of the plugin
     */
    public function __construct($settings, $name)
    {
        parent::__construct($settings, $name);
    }

    /**
     * Add the settings page to the admin menu
     *
     * @param string $parent The parent menu slug
     * @return bool True if the settings page was added, false otherwise
     */
    public function settings($parent)
    {
        global $wp_roles;

        addElement('label', $parent, [], 'Who should see the statistics?');
        addElement('br', $parent);

        foreach ($wp_roles->role_names as $key => $name) {
            $label  = addElement('label', $parent, [], $name);
            addElement('br', $parent);

            $attributes = [
                'type'  => 'checkbox',
                'name'  => 'view-rights[]',
                'value' => $key
            ];

            if (in_array($key, $this->settings['view-rights'] ?? [])) {
                $attributes['checked'] = 'checked';
            }

            addElement('input', $label, $attributes, '', 'afterBegin');
        }

        return true;
    }

    /**
     * Function to display the emails page
     *
     * @param   string  $parent The parent menu slug
     * 
     * @return  bool            True if the emails page was displayed, false otherwise
     */
    public function emails($parent)
    {
        return false;
    }

    public function data($parent = '')
    {
        if (!isset($_POST['exclude-list'])) {
            $_POST['exclude-list']    = '';
        }

        global $wpdb;

        wp_enqueue_script('tsjippy_statistics_admin', TSJIPPY\pathToUrl(PLUGINPATH . 'js/admin.min.js'), array(), PLUGINVERSION, true);

        $tableName    = $wpdb->prefix . 'tsjippy_statistics';

        // base query
        $query        = "SELECT `time_created`,`time_last_edited`, `url`, SUM(`counter`) as amount, count(`user_id`) as count FROM %i WHERE `url` NOT LIKE '/?%' AND `url` NOT LIKE '?%' AND `url` NOT LIKE '%/#%'";
        $values    = [
            $tableName
        ];

        // parse optional queries
        if (isset($_POST['exclude-editors'])) {
            // Exclude editors
            $users          = get_users(array(
                'role'        => ['editor'],
                'fields'    => 'ID'
            ));

            $placeholders   = implode(', ', array_fill(0, count($users), '%d'));
            $query        .= " AND `user_id` NOT IN ($placeholders)";
            $values        = array_merge($values, $users);
        }

        if (!empty($_POST['months'])) {
            $months     = (int) $_POST['months'];
            $minDate    = gmdate('Y-m-d', strtotime("- {$months}months"));
            $query        .= " AND `time_last_edited` > %s";
            $values[]     = $minDate;
        }

        if (!empty($_POST['exclude-list'])) {
            $placeholders   = implode(', ', array_fill(0, count(TSJIPPY\sanitize($_POST['exclude-list'])), '%d'));
            $query        .= " AND `url` NOT IN ($placeholders)";
            $values        = array_merge($values, TSJIPPY\sanitize($_POST['exclude-list']));
        }

        $query        .= " GROUP BY `url` ORDER BY `amount` DESC";

        $limit    = 100;
        if (is_numeric($_POST['max'] ?? '')) {
            $limit    = (int) $_POST['max'];
        }
        $query        .= " LIMIT $limit";

        // Get the results
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $pageViews  = $wpdb->get_results(
            $wpdb->prepare(
                $query,
                ...$values
            )
        );

        ob_start();
?>
        <div class='pagestatistics'>
            <h2>Statistics</h2>

            <form method='post' id='statistics-overview-settings'>
                <input type='hidden' class='no-reset' name='exclude-list' id='exclude-list' value='<?php echo TSJIPPY\sanitize($_POST['exclude-list'] ?? [] ); ?>'>
                <label>
                    <input type='checkbox' name='exclude-editors' value=1 <?php if (!empty($_POST['exclude-editors'])) echo ' checked'; ?>>
                    Exclude editors
                </label>
                <br>
                <label>
                    Show Statistics from the last <input type='number' name='months' value='<?php echo TSJIPPY\sanitize($_POST['months'] ?? ''); ?>' style='max-width: 60px;'> months only
                </label>
                <br>
                <label>
                    Show top <input type='number' name='max' value='<?php echo !isset($_POST['max']) ? 100 : (int) $_POST['max']; ?>' style='max-width: 60px;'> pages only
                </label>
                <br>
                <input type='submit' value='Apply'>
            </form>

            <table class='statistics-table tsjippy table'>
                <thead>
                    <th>URL</th>
                    <th>Total views</th>
                    <th>Unique views</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    <?php
                    foreach ($pageViews as $page) {
                    ?>
                        <tr>
                            <td class='url'>
                                <a href='<?php echo esc_url($page->url);?>'>
                                    <?php echo esc_html(explode('?', $page->url)[0]);?>
                                </a>
                            </td>
                            <td class='total-views'>
                                <?php echo $page->amount ?>
                            </td>
                            <td class='unique-views'>
                                <?php echo esc_attr($page->count); ?>
                            </td>
                            <td class='actions'>
                                <button class='small exclude-url' value='<?php echo esc_attr($page->url); ?>'>
                                    Exclude
                                </button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php

        addRawHtml(ob_get_clean(), $parent);

        return true;
    }

    /**
     * Add the functions page to the admin menu
     *
     * @param string $parent The parent menu slug
     * 
     * @return bool True if the functions page was added, false otherwise
     */
    public function functions($parent)
    {
        return false;
    }
}
