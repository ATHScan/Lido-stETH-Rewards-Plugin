<div id="reward_history_settings_wrapper" class="wrap">
 
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <hr />

    <form method="post" action="">
        
        <table class="form-table" role="presentation">

            <tr>

            	<th>Wallet Address</th>
            	<td>
            		<input type="text" id="reward_history_wallet_address" class="large-text" name="reward_history_wallet_address" placeholder="0x" required />
            	</td>

            </tr>

        </table>

        <p class="submit">
            <input type="submit" name="reward_history_submit_url" id="reward_history_submit_url" class="button button-primary" value="Add wallet address" />
        </p>

    </form>

    <?php if(count($message) > 0 && $message['status'] == 'success') { ?>

	    <div class="updated notice">
	    	<p><?php echo $message['message']; ?></p>
	    </div>

	<?php } else if(count($message) > 0 && $message['status'] == 'error') { ?>

		<div class="notice notice-warning">
	    	<p><?php echo $message['message']; ?></p>
	    </div>

	<?php } ?>

    <div class="clear"></div>

</div>

<?php if( count($all_wallet_addresses) > 0 ){ ?>

	<div id="reward_history_wallet_addresses_list" class="wrap">

		<table class="wp-list-table widefat striped table-view-list ">

			<thead>

				<tr>

					<th>Wallet Address</th>
					<th>Created Date</th>
					<th>Reward History Data</th>
					<th></th>

				</tr>

			</thead>

			<tbody>

				<?php foreach($all_wallet_addresses as $wallet_address_info) { ?>

					<tr id="wallet_address_row_<?php echo $wallet_address_info->id; ?>">

						<td><?php echo $wallet_address_info->wallet_address; ?></td>
						<td><?php echo date('Y/m/d \a\t H:i a', strtotime($wallet_address_info->created_date)); ?></td>
						<td><?php echo $wallet_address_info->reward_history_count; ?></td>
						<td><a href="javascript:;" class="delete_wallet_address" data-id="<?php echo $wallet_address_info->id; ?>">Delete</a></td>

					</tr>

				<?php } ?>

			</tbody>

		</table>

	</div>

<?php } ?>

<script type="text/javascript">

    (function($){

        if($('.delete_wallet_address').length){

            $('.delete_wallet_address').on('click', function(){

            	var wallet_address_id = $(this).attr('data-id');

                $.ajax({ 
                    data: {action: 'delete_wallet_address', wallet_id: wallet_address_id},
                    type: 'post',
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    success: function(response) {

                    	if(response == 'success'){

                    		$('#wallet_address_row_' + wallet_address_id).slideUp('fast', function(){
	                        	$(this).remove();
	                        });

                    	}

                    }
                });

            });

        }

    })(jQuery);

</script>