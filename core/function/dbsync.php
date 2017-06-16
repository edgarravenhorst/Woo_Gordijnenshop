<?php

// This script adds hooks to enable syncing of write DML's (so no inserts)
// to another WP database.


/*
wp-config.php:

define('WP_DEBUG_LOG', true );
define('SAVEQUERIES', true);

// Where to sync to:
define('SYNC_DB', 'wp2');							// MEERDERE DATABASES?
define('SYNC_DB_USER', 'homestead');
define('SYNC_DB_PASSWORD', 'secret');
define('SYNC_DB_HOST', 'localhost');
*/



// TODO: test and complete sync_actions list
$sync_actions = [
  'wp_loaded',
  'save_post', 'deleted_post', 'thrashed_post', 'unthrashed_post', 'updated_postmeta', 			// Posts
  'add_attachment', 'edit_attachment', 'delete_attachment',		// Attachments
  'add_category', 'delete_category', 													// Categories
  'created_term', 'edited_terms', 															// Terms
  'edited_term_taxonomy', 'deleted_term_taxonomy',							// Taxonomies
  'added_term_relationships', 'deleted_term_relationships',		// Relationships
  // Comments ???
  'delete_user', 'profile_update', 'user_register',						// Users
  'wp_login', 'wp_logout',
  ];

foreach ($sync_actions as $sync_action) {
  add_action($sync_action, 'do_db_sync');
}


function do_db_sync () {
  global $wpdb;

  $wpdb_slave = new wpdb(SYNC_DB_USER, SYNC_DB_PASSWORD, SYNC_DB, SYNC_DB_HOST);
  // TODO: fallbacks
  // TODO: check connection:   if ($wpdb_slave->check_connection()) ...

  // $wpdb->show_errors();
  // $wpdb->print_error();
  //
  // $wpdb_sync->show_errors();
  // $wpdb_sync->print_error();

  $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
  fwrite($log_file, "\n### " . date("F j, Y, g:i:s")."\n");

  foreach($wpdb->queries as $query){
    if(substr($query[0], 0, 6) == "SELECT") continue;           // TODO: met regexp

    $wpdb_slave->query($query[0]);
    fwrite($log_file, $query[0] . "\n");
  }

  fclose($log_file);
}
