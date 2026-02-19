<?php
/*
Plugin Name: Store Webqnna
Description: Store Webqnna Plugin.. no mare any explanation.
Version: 2.0.0
Author: webqnna.net
Author URI: https://www.webqnna.net/
*/

define('QS_DIR', dirname(__FILE__));
define('QS_URL', rtrim(rtrim(plugin_dir_url(__FILE__),'/'),'\\'));
foreach(glob( QS_DIR . '/classes/*.php') as $file) require_once($file);
foreach(glob( QS_DIR . '/includes/*.php') as $file) require_once($file);
foreach(glob( QS_DIR . '/shortcodes/*.php') as $file) require_once($file);
require_once( QS_DIR . '/functions.php');

qs::add_custom_slug('gift', QS_DIR . '/gift-images.php');

global $projects_slug;
$projects_slug = 'projects';