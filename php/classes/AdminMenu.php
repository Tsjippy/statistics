<?php
namespace TSJIPPY\STATISTICS;
use TSJIPPY;

use function TSJIPPY\addElement;
use function TSJIPPY\addRawHtml;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu extends TSJIPPY\ADMIN\SubAdminMenu{

    public function __construct($settings, $name){
        parent::__construct($settings, $name);
    }

    public function settings($parent){
        global $wp_roles;

        addElement('label', $parent, [], 'Who should see the statistics?');
        addElement('br', $parent);

        foreach($wp_roles->role_names as $key=>$name){
            $label  = addElement('label', $parent, [], $name);
            addElement('br', $parent);

            $attributes = [
                'type'  =>'checkbox', 
                'name'  =>'view-rights[]', 
                'value' => $key
            ];

            if(in_array($key, $this->settings['view-rights'] ?? [])){
                $attributes['checked'] = 'checked';
            }

            addElement('input', $label, $attributes, '', 'afterBegin');
        }

        return true;
    }

    public function emails($parent){
        return false;
    }

    public function data($parent=''){
        if(!isset($_POST['exclude-list'])){
            $_POST['exclude-list']	= '';
        }

        global $wpdb;

        $tableName	= $wpdb->prefix . 'tsjippy_statistics';

        // base query
        $query		= "SELECT `time_created`,`time_last_edited`, `url`, SUM(`counter`) as amount, count(`user_id`) as count FROM $tableName WHERE `url` NOT LIKE '/?%' AND `url` NOT LIKE '?%' AND `url` NOT LIKE '%/#%'";

        // parse optional queries
        if(isset($_POST['exclude-editors'])){
            // Exclude editors
            $users 		 = implode(',', get_users( array(
                'role'		=> ['editor'],
                'fields'	=> 'ID'
            ) ));
            $query		.= " AND `user_id` NOT IN ($users)";
        }

        if(!empty($_POST['months'])){
            $months     = (int) $_POST['months'];
            $minDate	= Date('Y-m-d', strtotime("- {$months}months"));
            $query		.= " AND `time_last_edited` > '$minDate'";
        }

        if(!empty($_POST['exclude-list'])){
            $query		.= " AND `url` NOT IN ('".str_replace(',', "','", $_POST['exclude-list'])."')";
        }

        $query		.= " GROUP BY `url` ORDER BY `amount` DESC";

        $limit	= 100;
        if(isset($_POST['max']) && is_numeric($_POST['max'])){
            $limit	= (int) $_POST['max'];
        }
        $query		.= " LIMIT $limit";

        // Get the results
        $pageViews  = $wpdb->get_results( $query );

        ob_start();
        // Add a script to add a page to the exclusion
        ?>
        <script>
            function addExclude(el){
                let form = document.getElementById('statistics-overview-settings');
                let excludeList	= form.querySelector('#exclude-list');
                if(excludeList.value != ''){
                    excludeList.value	= excludeList.value+','+el.value;
                }else{
                    excludeList.value	= el.value;
                }

                form.submit();
            }
        </script>
        <div class='pagestatistics'>
            <h2>Statistics</h2>

            <form method='post' id='statistics-overview-settings'>
                <input type='hidden' class='no-reset' name='exclude-list' id='exclude-list' value='<?php echo $_POST['exclude-list'];?>'>
                <label>
                    <input type='checkbox' name='exclude-editors' value=1 <?php if(!empty($_POST['exclude-editors'])){ echo ' checked';} ?>>
                    Exclude editors
                </label>
                <br>
                <label>
                    Show Statistics from the last <input type='number' name='months' value='<?php echo $_POST['months'];?>' style='max-width: 60px;'> months only
                </label>
                <br>
                <label>
                    Show top <input type='number' name='max' value='<?php if(!isset($_POST['max'])){echo 100;}else{echo $_POST['max'];}?>' style='max-width: 60px;'> pages only
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
                        foreach($pageViews as $page){
                            ?>
                            <tr>
                                <td class='url'><?php echo "<a href='$page->url'>".explode('?', $page->url)[0]."</a>";?></td>
                                <td class='total-views'><?php echo $page->amount?></td>
                                <td class='unique-views'><?php echo $page->count;?></td>
                                <td class='actions'><button class='small exclude-url' value='<?php echo $page->url; ?>' onclick='addExclude(this)'>Exclude</button></td>
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

    public function functions($parent){

        return false;
    }
}