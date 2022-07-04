jQuery(document).ready(function () {


	if (jQuery(".smp_survey-item-action-disabled").length > 0) {
		jQuery(".smp_survey-item-action-disabled .smp_survey-vote-button").addClass("smp_disabled-button");
		jQuery(".smp_survey-item-action-disabled .smp_disabled-button").removeClass("smp_survey-vote-button");
	}

	jQuery('.smp_option-name.live').on('click', function () {
		jQuery('.smp_option-name.live').removeClass('smp_fill-option');
		jQuery(this).addClass('smp_fill-option');

		var activeId = jQuery(this).attr('id');
		localStorage.setItem('active_option_id', activeId);

	});

	var activeOptionId = localStorage.getItem('active_option_id');
	if (jQuery(".smp_survey-item-action-disabled").length > 0) {
		jQuery(`.smp_survey-item-action-disabled #${activeOptionId}`).addClass('smp_fill-option');
	}



	jQuery('.smp_survey-item').each(function () {
		var smp_item = jQuery(this);
		jQuery(this).find('.smp_survey-vote-button.live').click(function () {

			jQuery(smp_item).parent().find('.smp_survey-item').each(function () {
				jQuery(this).find('.smp_survey-vote-button').attr('disabled', 'yes');

			});

			var smp_btn = jQuery(this);
			
			var data = {
				'action': 'smp_vote',
				'option_id': jQuery(smp_item).find('.smp_survey-item-id').val(),
				'poll_id': jQuery(smp_item).find('.smp_poll-id').val() // We pass php values differently!
			};

			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			jQuery.post(smp_ajax_obj.ajax_url, data, function (response) {

				var smp_json = jQuery.parseJSON(response);
				console.log(smp_json);

				jQuery(smp_item).parent().find('.smp_survey-item').each(function () {
					jQuery(this).find('.smp_survey-vote-button').addClass('smp_scale_hide');
				});

				jQuery('.smp_survey-progress-fg').attr('style', 'width:' + Math.abs(100 - smp_json.total_vote_percentage) + '%');
				
				jQuery(smp_item).find('.smp_survey-progress-fg').attr('style', 'width:' + smp_json.total_vote_percentage + '%');

				
				jQuery('.smp_survey-progress-label').text(Math.abs(100 - smp_json.total_vote_percentage) + '%');
				jQuery(smp_item).find('.smp_survey-progress-label').text(smp_json.total_vote_percentage + '%');

				jQuery('.smp_user-partcipeted').text('Thank you for participating.');
				jQuery('.smp_survey-total-vote h3 span').text(smp_json.total_vote_count);

			});

		});

	});

});