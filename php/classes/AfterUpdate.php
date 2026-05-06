<?php
namespace TSJIPPY\STATISTICS;
use TSJIPPY;

if ( ! defined( 'ABSPATH' ) ) exit;

class AfterUpdate extends TSJIPPY\AfterPluginUpdate {

    public function afterPluginUpdate($oldVersion){
        global $wpdb;

        TSJIPPY\printArray('Running update actions');

        if(version_compare('10.0.4', $oldVersion)){
            /**
             * Rename tables to tsjippy_
             */
            $wpdb->query(
                "ALTER TABLE `{$wpdb->prefix}tsjippy_statistics`
                RENAME COLUMN `timecreated` to `time_created`,
                RENAME COLUMN `timelastedited` to `time_last_edited`,
                RENAME COLUMN `userid` to `user_id`;"
            );
        }
    }
}
