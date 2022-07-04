<?php
/*
Plugin Name: Simple Poll
Plugin Uri: https://github.com/akashmdiu/smp-simple-poll
Description: The Simple Poll is a voting poll system into your post, pages and everywhere in website by just a shortcode. Add poll system to your post by placing shortcode.
Author: Akash Mia
Author URI: https://www.bprogrammer.net
Version: 1.0.0
Tags: simple poll, voting poll, survay, poll by shortcode, create poll.
Text Domain: simple-poll
Licence: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


/*###############################################################
    Simple Poll 1.0.0 A simple poll system for WordPress
##############################################################*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path(__FILE__) . 'src/init.php';

/**
 *  Initializer.
 */
require_once plugin_dir_path(__FILE__) . 'smp-simple-poll.php';
