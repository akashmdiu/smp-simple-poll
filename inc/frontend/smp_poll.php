<?php
add_shortcode('SIMPLE_POLL', 'smp_add_shortcode');
function smp_add_shortcode($atts, $content = null)
{
	$a = shortcode_atts(array(
		'id' => '1',
		'type' => '',
		'use_in' => 'post'
	), $atts);

	$smp_shortcode_args = array(
		'post_type'              => array('smp_poll'),
		'post_status'            => array('publish'),
		'nopaging'               => true,
		'order'                  => 'DESC',
		'orderby'                => 'date',
		'p'                      => $a['id']
	);

	// The Query
	$smp_post_query = new WP_Query($smp_shortcode_args);
	// The Loop
	ob_start();
	if ($smp_post_query->have_posts()) {

		while ($smp_post_query->have_posts()) : $smp_post_query->the_post();

			$smp_option_names = array();
			if (get_post_meta(get_the_id(), 'smp_poll_option', true)) {
				$smp_option_names = get_post_meta(get_the_id(), 'smp_poll_option', true);
			}
		
			$smp_poll_status = get_post_meta(get_the_id(), 'smp_poll_status', true);
			$smp_display_poll_result = get_post_meta(get_the_id(), 'smp_display_poll_result', true);
			$smp_poll_option_id = get_post_meta(get_the_id(), 'smp_poll_option_id', true);
			$smp_poll_end_date = get_post_meta(get_the_id(), 'smp_end_date', true);
			$smp_poll_vote_total_count = (int) get_post_meta(get_the_id(), 'smp_vote_total_count', true); 
			
			$has_live = '';
			if($smp_poll_status === 'live'){
				$has_live = ' live';
			}
			$name_option = '';
			if (!smp_check_for_unique_voting(get_the_id(), $smp_poll_option_id[0])){
				$name_option = ' smp_option-name';
			} 
			?>
			<div class="smp_container text-align-center">
		
		<div class="smp_survey-stage">
				<h3 class="smp_stage smp_live smp_active" <?php if ($smp_poll_status !== 'live') echo 'style="display:none;"'; ?>><?php echo esc_html__('Live', 'simple-poll'); ?></h3>
				<h3 class="smp_stage smp_ended smp_active" <?php if ($smp_poll_status !== 'end') echo 'style="display:none;"'; ?>><?php echo esc_html__('Ended', 'simple-poll'); ?></h3>
		</div>
		<div class="smp_title">

			<div class="smp_poll-title">
				<h1><?php the_title();  ?></h1>
			</div>

		<?php if($smp_display_poll_result === 'public'): ?>
			<div class="smp_survey-total-vote">
				<h3> <?php echo wp_kses_post('Total Vote: ' .'<span>'. $smp_poll_vote_total_count.'</span>'); ?></h3>
			</div>
		<?php endif; ?>
		</div>
		<div class="smp_inner">

			<ul class="smp_surveys">

				<?php if ($smp_poll_status !== 'end'): ?>
					<li class="smp_poll-end-time text-align-center">
						<p><?php echo esc_html__('Will End : ' . date("M d, Y", strtotime($smp_poll_end_date)), 'simple-poll'); ?></p>
					</li>
				<?php endif; ?>

				<?php
					$i = 0;
					if ($smp_option_names) {
						foreach ($smp_option_names as $smp_option_name) :
							$smp_poll_vote_count =  get_post_meta(get_the_id(), 'smp_vote_count_' . (float) $smp_poll_option_id[$i], true);

							$smp_poll_vote_percentage = 0;
							if ($smp_poll_vote_count == 0) {
								$smp_poll_vote_percentage = 0;
							} else {
								if($smp_poll_vote_total_count > 0){
									$smp_poll_vote_percentage = round( (float) $smp_poll_vote_count * 100 / $smp_poll_vote_total_count);
								}
							}
							$smp_poll_vote_percentage = round( (float) $smp_poll_vote_percentage);
							?>
						<li class="smp_survey-item">

							<div class="smp_survey-item-inner smp_card_front">
								<div class="smp_survey-item-action<?php if (smp_check_for_unique_voting(get_the_id(), $smp_poll_option_id[$i])) echo ' smp_survey-item-action-disabled'; ?>">
									<form action="" name="smp_survey-item-action-form" class="smp_survey-item-action-form">
										<input type="hidden" name="smp_poll-id" class="smp_poll-id" value="<?php echo esc_attr(get_the_id()); ?>">
										<input type="hidden" name="smp_survey-item-id" class="smp_survey-item-id" value="<?php echo esc_attr($smp_poll_option_id[$i]); ?>">

										<input type="button" role="vote" name="smp_survey-vote-button" class="smp_survey-vote-button <?php echo esc_attr( $has_live ); echo esc_attr( $name_option); ?>" id="smp_option-id-<?php echo esc_attr($i) ?>">
									</form>

									<div class="smp_survey-name">
										<h5><?php echo esc_html($smp_option_name); ?></h5>
									</div>
								</div>


								<div class="smp_pull-right">

									<div class="smp_survey-progress">
										<div class="smp_survey-progress-bg">
											<div class="smp_survey-progress-fg smp_orange_gradient" <?php if($smp_display_poll_result === 'public'): ?>style="width:<?php echo esc_attr($smp_poll_vote_percentage); ?>%;" <?php endif;?> >
											</div>
											<?php if($smp_display_poll_result === 'public'): ?>
												<div class="smp_survey-progress-label">
													<?php echo esc_html($smp_poll_vote_percentage); ?>%
												</div>
											<?php endif; ?>
										</div>

									</div>
								</div>


							</div>
						</li>

				<?php
							$i++;
						endforeach;
						echo '</ul> <div style="clear:both;"></div>';
					} else {
						if (current_user_can('administrator')) {
							_e('<p class="smp_short_code">Please add some questions or may be you missed the option field.</p><br><a href="' . get_edit_post_link(get_the_id()) . '" class="smp_survey-notfound-button" style="width:auto;max-width:100%;">Edit This Poll</a>', 'simple-poll');
						} else {
							_e('<p class="smp_short_code">This Poll is not yet ready contact site administrator</p>', 'simple-poll');
						}
					} ?>

		</div>



		<div class="smp_user-partcipeted">
			<?php if ($smp_option_names) :
					foreach ($smp_option_names as $smp_option_name) :
						if (smp_check_for_unique_voting(get_the_id(), $smp_poll_option_id[0])) : ?>
						<p> <?php echo esc_html__('You already partcipeted.', 'simple-poll'); ?></p>
			<?php endif;
						break;
					endforeach;
				endif; ?>
		</div>
	</div>
		<?php endwhile;
	}

	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	// Restore original Post Data
	wp_reset_postdata();
}
add_filter('widget_text', 'do_shortcode');
add_filter('content', 'do_shortcode');
