(function($){

	$('#reward_history_input').bind('paste', function(e){

		var wallet_address = e.originalEvent.clipboardData.getData('text');

		$('#reward_history_result_notice').hide();
		$('#reward_spinner').show();

		$.ajax({
			'url': reward_history_params.url,
			'type': 'POST',
			'data': {
				'action' : 'reward_history_results',
				'wallet_address' : wallet_address
			},
			success: function(response){

				$('#reward_spinner').hide();

				if(response != ""){

					$('#reward_history_result_table').html(response);

					var reward_history_default_content = JSON.parse($('#reward_history_result_table').find('#reward_history_default_content_hidden').text());

					var steth_balance = (parseFloat(reward_history_default_content.balance) / 1e18).toLocaleString('en-US', {maximumFractionDigits : 8});
					var steth_balance_fixed = ((parseFloat(reward_history_default_content.balance) / 1e18) * parseFloat(reward_history_default_content.stETHCurrencyPrice.usd)).toLocaleString('en-US', {maximumFractionDigits : 2});
					var average_apr = parseFloat(reward_history_default_content.averageApr).toFixed(1) + '%';
					var steth_earned = (parseFloat(reward_history_default_content.totals.ethRewards) / 1e18).toLocaleString('en-US', {maximumFractionDigits : 7});
					var steth_earned_rewards = parseFloat(reward_history_default_content.totals.currencyRewards).toLocaleString('en-US', {maximumFractionDigits : 2});
					var steth_price_usd = parseFloat(reward_history_default_content.stETHCurrencyPrice.usd).toLocaleString('en-US');
					var steth_price_eth = parseFloat(reward_history_default_content.stETHCurrencyPrice.eth).toFixed(8);

					$('#rh_dc_steth_balance .default_content_item_change_value_prefix_val').text(steth_balance);
					$('#rh_dc_steth_balance .default_content_item_fixed_value_prefix_val').text(steth_balance_fixed);
					$('#rh_dc_average_apr .default_content_item_change_value_prefix_val').text(average_apr);
					$('#rh_dc_steth_earned .default_content_item_change_value_prefix_val').text(steth_earned);
					$('#rh_dc_steth_earned .default_content_item_fixed_value_prefix_val').text(steth_earned_rewards);
					$('#rh_dc_steth_price .default_content_item_change_value_prefix_val').text(steth_price_usd);
					$('#rh_dc_steth_price .default_content_item_fixed_value_prefix_val').text(steth_price_eth);

					$('#reward_history_result_table table').DataTable({
						searching: false,
						info: false,
						lengthChange: false,
						ordering: false
					});

				} else {
					$('#reward_history_result_notice').show();
				}

			}
		});

	});

	$(window).on('load', function(){

		if($('#all_wallet_reward_history .multi_reward_history_list_wrapper').length){

			$('#all_wallet_reward_history .multi_reward_history_list_wrapper').each(function(){

				var current_obj = $(this);
				var wallet_address = $.trim($(this).find('.multi_reward_history_wallet_address').text());

				current_obj.find('.multi_reward_spinner').show();

				$.ajax({
					'url': reward_history_params.url,
					'type': 'POST',
					'data': {
						'action' : 'reward_history_results',
						'wallet_address' : wallet_address
					},
					success: function(response){

						current_obj.find('.multi_reward_spinner').hide();

						if(response != ""){

							var history_result_table = current_obj.find('.multi_reward_history_result_table');

							history_result_table.html(response);

							var reward_history_default_content = JSON.parse(history_result_table.find('.reward_history_default_content_hidden').text());

							var steth_balance = (parseFloat(reward_history_default_content.balance) / 1e18).toLocaleString('en-US', {maximumFractionDigits : 8});
							var steth_balance_fixed = ((parseFloat(reward_history_default_content.balance) / 1e18) * parseFloat(reward_history_default_content.stETHCurrencyPrice.usd)).toLocaleString('en-US', {maximumFractionDigits : 2});
							//var average_apr = parseFloat(reward_history_default_content.averageApr).toFixed(1) + '%';
							var steth_earned = (parseFloat(reward_history_default_content.totals.ethRewards) / 1e18).toLocaleString('en-US', {maximumFractionDigits : 7});
							var steth_earned_rewards = parseFloat(reward_history_default_content.totals.currencyRewards).toLocaleString('en-US', {maximumFractionDigits : 2});
							var steth_price_usd = parseFloat(reward_history_default_content.stETHCurrencyPrice.usd).toLocaleString('en-US');
							var steth_price_eth = parseFloat(reward_history_default_content.stETHCurrencyPrice.eth).toFixed(8);

							current_obj.find('.multi_rh_dc_steth_balance .multi_default_content_item_change_value_prefix_val').text(steth_balance);
							current_obj.find('.multi_rh_dc_steth_balance .multi_default_content_item_fixed_value_prefix_val').text(steth_balance_fixed);
							//current_obj.find('.multi_rh_dc_average_apr .multi_default_content_item_change_value_prefix_val').text(average_apr);
							current_obj.find('.multi_rh_dc_steth_earned .multi_default_content_item_change_value_prefix_val').text(steth_earned);
							current_obj.find('.multi_rh_dc_steth_earned .multi_default_content_item_fixed_value_prefix_val').text(steth_earned_rewards);
							current_obj.find('.multi_rh_dc_steth_price .multi_default_content_item_change_value_prefix_val').text(steth_price_usd);
							current_obj.find('.multi_rh_dc_steth_price .multi_default_content_item_fixed_value_prefix_val').text(steth_price_eth);

							history_result_table.find('table').DataTable({
								searching: false,
								info: false,
								lengthChange: false,
								ordering: false
							});

						}

					}

				});

			});

		}

	});

})(jQuery);