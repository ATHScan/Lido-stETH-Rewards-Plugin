<div id="reward_history_list_wrapper">

	<h1 id="reward_history_heading_title">Reward History</h1>
	<p id="reward_history_heading_description">Track your Ethereum staking rewards.</p>

	<div id="reward_history_form_wrapper">

		<div id="reward_history_input_wrapper">

			<label for="reward_history_input">
				<span><input type="text" name="reward_history_input" id="reward_history_input" placeholder="Ethereum address" /></span>
			</label>

			<div class="reward_history_warning">Current balance may differ from last balance in the table due to rounding.</div>

		</div>

		<div id="reward_history_default_content_wrapper">

			<div id="reward_history_default_content">

				<div id="rh_dc_steth_balance" class="default_content_item">

					<div class="default_content_item_title">stETH balance</div>
					<div class="default_content_item_change_values">
						<div class="default_content_item_change_value_prefix">Ξ</div>
						<div class="default_content_item_change_value_prefix_val">0</div>
					</div>

					<div class="default_content_item_fixed_values">
						<div class="default_content_item_fixed_value_prefix">$</div>
						<div class="default_content_item_fixed_value_prefix_val">0</div>
					</div>

				</div>

				<div id="rh_dc_steth_earned" class="default_content_item">

					<div class="default_content_item_title">stETH earned</div>
					<div class="default_content_item_change_values">
						<div class="default_content_item_change_value_prefix">Ξ</div>
						<div class="default_content_item_change_value_prefix_val">0</div>
					</div>

					<div class="default_content_item_fixed_values">
						<div class="default_content_item_fixed_value_prefix">$</div>
						<div class="default_content_item_fixed_value_prefix_val">0</div>
					</div>

				</div>

				<div id="rh_dc_average_apr" class="default_content_item">

					<div class="default_content_item_title">Average APR</div>
					<div class="default_content_item_change_values">
						<div class="default_content_item_change_value_prefix_val">-</div>
					</div>

				</div>

				<div id="rh_dc_steth_price" class="default_content_item">

					<div class="default_content_item_title">stETH price</div>
					<div class="default_content_item_change_values">
						<div class="default_content_item_change_value_prefix">$</div>
						<div class="default_content_item_change_value_prefix_val">0</div>
					</div>

					<div class="default_content_item_fixed_values">
						<div class="default_content_item_fixed_value_prefix">Ξ</div>
						<div class="default_content_item_fixed_value_prefix_val">0</div>
					</div>

				</div>

			</div>

		</div>

	</div>

	<div id="reward_history_result_wrapper">

		<h4>Stake History</h4>
		<div id="reward_history_result_table"></div>
		<div id="reward_history_result_notice">Enter your Ethereum address to see the stats.</div>
		<div id="reward_spinner"></div>

	</div>

</div>