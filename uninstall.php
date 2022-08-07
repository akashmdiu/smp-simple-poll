<?php

/**
 * Trigger this file on plugin uninstall
 * @package SmppSimplePoll
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}


//Clrear Database stored data
// $polls = get_posts(array('post_type' => 'smp_poll', 'numberposts' => -1));

// foreach ($polls as $poll){
//     wp_delete_post( $poll->ID, true )
// }

//Access the database via SQL
global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'smp_poll'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN(SELECT id FROM wp_posts)");
