<?php


if (!function_exists('smp_poll_is_public')) {
	function smp_poll_is_public($smp_display_poll_result)
	{
		if ($smp_display_poll_result === 'public') {
			return true;
		}
		return false;
	}
}
if (!function_exists('smp_poll_is_public_after_vote')) {
	function smp_poll_is_public_after_vote($smp_display_poll_result)
	{
		if ($smp_display_poll_result === 'public_after_vote') {
			return true;
		}
		return false;
	}
}


if (!function_exists('smp_poll_simple_poll_cpt')) {
	function smp_poll_simple_poll_cpt()
	{

		$labels = array(
			'name'                => _x('Simple Poll', 'smp-simple-poll'),
			'singular_name'       => _x('Simple Poll',  'smp-simple-poll'),
			'menu_name'           => __('Simple Polls', 'smp-simple-poll'),
			'name_admin_bar'      => __('Simple Polls', 'smp-simple-poll'),
			'parent_item_colon'   => __('Parent Poll:', 'smp-simple-poll'),
			'all_items'           => __('All Polls', 'smp-simple-poll'),
			'add_new_item'        => __('Add New Poll', 'smp-simple-poll'),
			'add_new'             => __('Add New', 'smp-simple-poll'),
			'new_item'            => __('New Poll', 'smp-simple-poll'),
			'edit_item'           => __('Edit Poll', 'smp-simple-poll'),
			'update_item'         => __('Update Poll', 'smp-simple-poll'),
			'view_item'           => __('View Poll', 'smp-simple-poll'),
			'search_items'        => __('Search Poll', 'smp-simple-poll'),
			'not_found'           => __('Not found', 'smp-simple-poll'),
			'not_found_in_trash'  => __('Not found in Trash', 'smp-simple-poll'),
		);
		$args = array(
			'label'               => __('Simple Poll', 'smp-simple-poll'),
			'description'         => __('Simple Poll Description', 'smp-simple-poll'),
			'labels'              => $labels,
			'supports'            => array('title', 'revisions'),
			'show_in_rest' 		  => true,
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'			  => 'dashicons-chart-pie',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite' 			  => array('slug' => 'poll'),
			'capability_type'     => 'page',
		);
		register_post_type('smp_poll', $args);
		flush_rewrite_rules(true);
	}

	// Hook into the 'init' action
	add_action('init', 'smp_poll_simple_poll_cpt', 0);
}


/**
 * Remove menu item for on Administrator
 */
if (!function_exists('smp_poll_remove_menu_items')) {
	function smp_poll_remove_menu_items()
	{
		if (!current_user_can('administrator')) :
			remove_menu_page('edit.php?post_type=smp_poll');
		endif;
	}
	add_action('admin_menu', 'smp_poll_remove_menu_items');
}

/**
 * Add Simple Poll Admin Style
 */
if (!function_exists('smp_poll_css_register')) {
	add_action('admin_enqueue_scripts', 'smp_poll_css_register', 1);
	function smp_poll_css_register()
	{
		wp_register_style('smp-poll-backend', plugins_url('/assets/css/smp-poll-backend.css', __FILE__));
		wp_enqueue_style(array('smp-poll-backend'));
	}
}

/**
 * Add SMP Frontend Style
 */
if (!function_exists('smp_poll_enqueue_style')) {

	add_action('wp_enqueue_scripts', 'smp_poll_enqueue_style');
	function smp_poll_enqueue_style()
	{
		wp_enqueue_style('smp-poll-frontend', plugins_url('/assets/css/smp-poll-frontend.css', __FILE__), false, rand(23344, 43435));
	}
}

//Add SMP Frontend Script
if (!function_exists('smp_poll_enqueue_script')) {
	add_action('wp_enqueue_scripts', 'smp_poll_enqueue_script', 1);
	function smp_poll_enqueue_script()
	{
		wp_enqueue_script('smp-poll-ajax', plugins_url('/assets/js/smp-ajax-poll.js', __FILE__), array('jquery'), rand(23344, 43435));

		wp_localize_script('smp-poll-ajax', 'smp_poll_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));
		wp_enqueue_script('smp-poll-frontend', plugins_url('/assets/js/smp-poll-frontend.js', __FILE__), false, rand(23344, 43435));
	}
}



include_once('inc/backend/smp_poll_metaboxes.php');

include_once('inc/frontend/smp_poll.php');

if (!function_exists('get_smp_poll_template')) {

	add_filter('single_template', 'get_smp_poll_template');
	function get_smp_poll_template($single_template)
	{
		global $post;

		if ($post->post_type == 'smp_poll') {
			$single_template = dirname(__FILE__) . '/inc/frontend/smp_poll-template.php';
		}
		return $single_template;
	}
}

if (!function_exists('smp_poll_ajax_smp_vote')) {

	add_action('wp_ajax_smp_vote', 'smp_poll_ajax_smp_vote');
	add_action('wp_ajax_nopriv_smp_vote', 'smp_poll_ajax_smp_vote');

	function smp_poll_ajax_smp_vote()
	{

		if (isset($_POST['action']) and $_POST['action'] == 'smp_vote') {

			if (isset($_POST['poll_id'])) {
				$poll_id = intval(sanitize_text_field($_POST['poll_id']));
			}

			if (isset($_POST['option_id'])) {
				$option_id = (float) sanitize_text_field($_POST['option_id']);
			}


			//Validate Poll ID
			if (!$poll_id) {
				$poll_id = '';
				die(json_encode(array("voting_status" => "error", "msg" => "Fields are required")));
			}

			//Validate Option ID
			if (!$option_id) {
				$option_id = '';
				die(json_encode(array("voting_status" => "error", "msg" => "Fields are required")));
			}

			$oldest_vote = 0;
			$oldest_total_vote = 0;
			if (get_post_meta($poll_id, 'smp_vote_count_' . $option_id, true)) {
				$oldest_vote = get_post_meta($poll_id, 'smp_vote_count_' . $option_id, true);
			}
			if (get_post_meta($poll_id, 'smp_vote_total_count')) {
				$oldest_total_vote = get_post_meta($poll_id, 'smp_vote_total_count', true);
			}

			if (!smp_poll_check_for_unique_voting($poll_id)) {

				$new_total_vote = intval($oldest_total_vote) + 1;
				$new_vote = (int) $oldest_vote + 1;
				update_post_meta($poll_id, 'smp_vote_count_' . $option_id, $new_vote);
				update_post_meta($poll_id, 'smp_vote_total_count', $new_total_vote);

				$outputdata = array();
				$outputdata['total_vote_count'] = $new_total_vote;
				$outputdata['total_opt_vote_count'] = $new_vote;
				$outputdata['option_id'] = $option_id;
				$outputdata['voting_status'] = "done";
				$outputdataPercentage = ($new_vote * 100) / $new_total_vote;
				$outputdata['total_vote_percentage'] = (int) $outputdataPercentage;

				print_r(json_encode($outputdata));
			}
		}
		die();
	}
}

/**
 * Adding Columns to Simple Poll CPT
 */
if (!function_exists('smp_poll_set_custom_edit_columns')) {
	add_filter('manage_smp_poll_posts_columns', 'smp_poll_set_custom_edit_columns');
	function smp_poll_set_custom_edit_columns($columns)
	{
		$columns['smp_poll_id'] = __('Poll ID', 'smp-simple-poll');
		$columns['poll_status'] = __('Poll Status', 'smp-simple-poll');
		$columns['shortcode'] = __('Shortcode', 'smp-simple-poll');
		$columns['view_result'] = __('Result(Yes/No)', 'smp-simple-poll');
		return $columns;
	}
}

if (!function_exists('smp_poll_custom_column')) {
	// Add the data to the custom columns for the smp_poll post type:
	add_action('manage_smp_poll_posts_custom_column', 'smp_poll_custom_column', 10, 2);
	function smp_poll_custom_column($column, $post_id)
	{
		switch ($column) {

			case 'shortcode':
				$code = '[SIMPLE_POLL id="' . $post_id . '"][/SIMPLE_POLL]';
				if (is_string($code))
					echo wp_kses_post('<code>' . $code . '</code>');
				else
					_e('Unable to get shortcode', 'smp-simple-poll');
				break;
			case 'poll_status':
				echo wp_kses_post("<span style='text-transform:uppercase'>" . get_post_meta(get_the_id(), 'smp_poll_status', true) . "</span>");
				break;
			case 'smp_poll_id':
				echo wp_kses_post("<span style='text-transform:uppercase'>" . esc_attr(get_the_id()) . "</span>");
				break;

			case 'view_result':
				$option_id = '';
				$option_id = get_post_meta($post_id, 'smp_poll_option_id', true);


				$count_yes = 0;
				$count_no = 0;

				if (!empty($option_id[0])) {
					if (get_post_meta($post_id, 'smp_vote_count_' . (float) $option_id[0], true)) {
						$count_yes = get_post_meta($post_id, 'smp_vote_count_' . (float) $option_id[0], true);
					}
				}
				if (!empty($option_id[1])) {
					if (get_post_meta($post_id, 'smp_vote_count_' . (float) $option_id[1], true)) {
						$count_no = get_post_meta($post_id, 'smp_vote_count_' . (float) $option_id[1], true);
					}
				}
				echo esc_html($count_yes . '/' . $count_no);
				break;
		}
	}
}


if (!function_exists('smp_poll_check_for_unique_voting')) {

	function smp_poll_check_for_unique_voting($poll_id)
	{

		if (isset($_COOKIE['is_voted_' . $poll_id])) {
			return true;
		} else {
			return false;
		}
	}
}

include_once('inc/backend/smp_poll_widget.php');
