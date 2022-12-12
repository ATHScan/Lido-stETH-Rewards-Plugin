<?php
/**
 * Plugin Name: Lido stETH Reward History 
 * Description: Lido stETH Reward History
 * Version: 1.0
 * Author: Occudentalis Research Ltd
 * Author URI: http://athscan.xyz
 */

class LidoSTETHRewardHistory {

    private $message = array();

    public function __construct() {

    	add_action( 'admin_menu', array( $this, 'reward_history_admin_menu' ) );
        add_action( 'init', array( $this, 'reward_history_init' ) );

        add_action('wp_ajax_delete_wallet_address', array( $this, 'ajax_delete_wallet_address') );
        add_action('wp_ajax_nopriv_delete_wallet_address', array( $this, 'ajax_delete_wallet_address') );

        add_action('wp_ajax_reward_history_results', array($this, 'get_reward_history_results'));
        add_action('wp_ajax_nopriv_reward_history_results', array($this, 'get_reward_history_results'));

        add_shortcode('reward_history', array($this, 'reward_history_shortcode') );
        add_shortcode('reward_history_all', array($this, 'reward_history_all_shortcode') );

        // Cron functionality
        add_action( 'reward_history_cron_hook', array($this, 'reward_history_cron') );

        if (!wp_next_scheduled('reward_history_cron_hook')) {
            wp_schedule_event( strtotime('23:00:00'), 'daily', 'reward_history_cron_hook' );
        }

    }

    public function reward_history_admin_menu() {

    	add_menu_page("Add Wallet Address", "Reward History", 'manage_options', "reward-history-page", array( $this, 'reward_history_render' ), 'dashicons-awards');

	}

    public function reward_history_init(){

        global $wpdb;

        if( is_admin() && isset($_POST['reward_history_wallet_address']) && $_POST['reward_history_wallet_address'] != "" ){

            $wallet_address = trim($_POST['reward_history_wallet_address']);

            // Check for duplicate
            $duplicate_check = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'reward_history_wallet_address` WHERE `wallet_address` = "' . $wallet_address . '"');

            if(!$duplicate_check){

                $wpdb->query('INSERT INTO `' . $wpdb->prefix . 'reward_history_wallet_address` (`wallet_address`, `created_date`) VALUES ("' . $wallet_address . '", "' . date('Y-m-d H:i:s') . '")');

                $this->message['status'] = 'success';
                $this->message['message'] = 'Wallet address (<b>' . $wallet_address . '</b>) inserted successfully.';

            } else {

                $this->message['status'] = 'error';
                $this->message['message'] = 'Wallet address (<b>' . $wallet_address . '</b>) already exists.';

            }

        }

    }

    public function reward_history_cron(){

        global $wpdb;

        // Get all wallet address from database

        $get_all_wallet_addresses = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'reward_history_wallet_address` ORDER BY `id` ASC');

        $rewards_history_data = array();

        if( count($get_all_wallet_addresses) > 0 ){

            foreach($get_all_wallet_addresses as $wallet_address_info){

                $wallet_address_data = file_get_contents('https://reward-history-backend.lido.fi/?address=' . $wallet_address_info->wallet_address . '&onlyRewards=true');

                $wallet_address_data = json_decode($wallet_address_data);

                if( count($wallet_address_data->events) > 0 ){

                    foreach($wallet_address_data->events as $reward_history_event){

                        if($reward_history_event->type == 'reward'){

                            // Check for duplicate
                            $duplicate_check = $wpdb->get_row('SELECT * FROM `' . $wpdb->prefix . 'reward_history_wallet_data` WHERE `wallet_address` = "' . $wallet_address_info->wallet_address . '" AND `reward_history_id` = "' . $reward_history_event->id . '" AND
                                `totalPooledEtherBefore` = "' . $reward_history_event->totalPooledEtherBefore . '" AND
                                `totalPooledEtherAfter` = "' . $reward_history_event->totalPooledEtherAfter . '" AND
                                `totalSharesBefore` = "' . $reward_history_event->totalSharesBefore . '" AND
                                `totalSharesAfter` = "' . $reward_history_event->totalSharesAfter . '" AND
                                `block` = "' . $reward_history_event->block . '" AND
                                `blockTime` = "' . $reward_history_event->blockTime . '" AND
                                `logIndex` = "' . $reward_history_event->logIndex . '" AND
                                `type` = "' . $reward_history_event->type . '" AND
                                `reportShares` = "' . $reward_history_event->reportShares . '" AND
                                `balance` = "' . $reward_history_event->balance . '" AND
                                `rewards` = "' . $reward_history_event->rewards . '" AND
                                `change` = "' . $reward_history_event->change . '" AND
                                `currencyChange` = "' . $reward_history_event->currencyChange . '" AND
                                `epochDays` = "' . $reward_history_event->epochDays . '" AND
                                `epochFullDays` = "' . $reward_history_event->epochFullDays . '" AND
                                `apr` = "' . $reward_history_event->apr . '"
                            ');

                            if(!$duplicate_check){

                                $wpdb->query('INSERT INTO `' . $wpdb->prefix . 'reward_history_wallet_data` (`wallet_address`, `reward_history_id`, `totalPooledEtherBefore`, `totalPooledEtherAfter`, `totalSharesBefore`, `totalSharesAfter`, `block`, `blockTime`, `logIndex`, `type`, `reportShares`, `balance`, `rewards`, `change`, `currencyChange`, `epochDays`, `epochFullDays`, `apr`)
                                    VALUES(
                                        "' . $wallet_address_info->wallet_address . '",
                                        "' . $reward_history_event->id . '",
                                        "' . $reward_history_event->totalPooledEtherBefore . '",
                                        "' . $reward_history_event->totalPooledEtherAfter . '",
                                        "' . $reward_history_event->totalSharesBefore . '",
                                        "' . $reward_history_event->totalSharesAfter . '",
                                        "' . $reward_history_event->block . '",
                                        "' . $reward_history_event->blockTime . '",
                                        "' . $reward_history_event->logIndex . '",
                                        "' . $reward_history_event->type . '",
                                        "' . $reward_history_event->reportShares . '",
                                        "' . $reward_history_event->balance . '",
                                        "' . $reward_history_event->rewards . '",
                                        "' . $reward_history_event->change . '",
                                        "' . $reward_history_event->currencyChange . '",
                                        "' . $reward_history_event->epochDays . '",
                                        "' . $reward_history_event->epochFullDays . '",
                                        "' . $reward_history_event->apr . '"
                                    )
                                ');

                            }

                        }

                    }

                }

            }

        }

        exit;

    }

    public function ajax_delete_wallet_address(){

        global $wpdb;

        if( is_admin() && isset($_POST['wallet_id']) && $_POST['wallet_id'] != "" ){

            $delete_wallet = $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'reward_history_wallet_address` WHERE `id` = "' . $_POST['wallet_id'] . '"');

            if($delete_wallet){
                echo 'success';
            } else {
                echo 'error';
            }

        }

        exit;

    }

    public function reward_history_render(){

        global $wpdb;

        $message = $this->message;

        $all_wallet_addresses = $wpdb->get_results('SELECT wa.*, COUNT(wd.wallet_address) AS reward_history_count FROM `' . $wpdb->prefix . 'reward_history_wallet_address` wa
            LEFT JOIN `' . $wpdb->prefix . 'reward_history_wallet_data` wd ON wd.wallet_address = wa.wallet_address
            GROUP BY wa.wallet_address
            ORDER BY `id` DESC');

        include_once( 'views/settings.php' );

    }

    public function reward_history_shortcode(){

        wp_enqueue_style( 'jquery-datatables', '//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css', null, time() );
        wp_enqueue_style( 'reward-history', plugin_dir_url( __FILE__ ) . '/assets/css/reward_history.css', null, time() );
        wp_enqueue_script( 'jquery-datatables', '//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js', '', time(), true );
        wp_enqueue_script( 'reward-history', plugin_dir_url( __FILE__ ) . '/assets/js/reward_history.js', '', time(), true );

        wp_localize_script('reward-history', 'reward_history_params', array(
            'url' => admin_url('admin-ajax.php'),
        ));

        ob_start();

        include_once( 'views/shortcode.php' );

        $shortcode_content = ob_get_contents();

        ob_end_clean();

        return $shortcode_content;

    }

    public function reward_history_all_shortcode(){

        global $wpdb;

        wp_enqueue_style( 'jquery-datatables', '//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css', null, time() );
        wp_enqueue_style( 'reward-history', plugin_dir_url( __FILE__ ) . '/assets/css/reward_history.css', null, time() );
        wp_enqueue_script( 'jquery-datatables', '//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js', '', time(), true );
        wp_enqueue_script( 'reward-history', plugin_dir_url( __FILE__ ) . '/assets/js/reward_history.js', '', time(), true );

        wp_localize_script('reward-history', 'reward_history_params', array(
            'url' => admin_url('admin-ajax.php'),
        ));

        $all_wallet_addresses = $wpdb->get_results('SELECT wa.*, COUNT(wd.wallet_address) AS reward_history_count FROM `' . $wpdb->prefix . 'reward_history_wallet_address` wa
            LEFT JOIN `' . $wpdb->prefix . 'reward_history_wallet_data` wd ON wd.wallet_address = wa.wallet_address
            GROUP BY wa.wallet_address
            ORDER BY `id` DESC');

        $shortcode_content = '';

        if(count($all_wallet_addresses) > 0){

            $shortcode_content .= '<div id="all_wallet_reward_history">';

            foreach($all_wallet_addresses as $wallet_address){

                if($wallet_address->reward_history_count > 0){

                    $get_wallet_address_other_info = $this->get_wallet_address_other_info($wallet_address->wallet_address);

                    $shortcode_content .= '<div class="multi_reward_history_list_wrapper">

                        <div class="multi_reward_history_form_wrapper">

                            <div class="multi_reward_history_input_wrapper">

                                <div class="multi_reward_history_wallet_address"><a href="https://etherscan.io/address/' . $wallet_address->wallet_address . '" target="_blank">' . $wallet_address->wallet_address . '</a></div>

                            </div>

                            <div class="multi_reward_history_default_content_wrapper">

                                <div class="multi_reward_history_default_content">

                                    <div class="multi_rh_dc_steth_balance multi_default_content_item">

                                        <div class="multi_default_content_item_title">stETH balance</div>
                                        <div class="multi_default_content_item_change_values">
                                            <div class="multi_default_content_item_change_value_prefix">Ξ</div>
                                            <div class="multi_default_content_item_change_value_prefix_val">0</div>
                                        </div>

                                        <div class="multi_default_content_item_fixed_values">
                                            <div class="multi_default_content_item_fixed_value_prefix">$</div>
                                            <div class="multi_default_content_item_fixed_value_prefix_val">0</div>
                                        </div>

                                    </div>

                                    <div class="multi_rh_dc_steth_earned multi_default_content_item">

                                        <div class="multi_default_content_item_title">stETH earned</div>
                                        <div class="multi_default_content_item_change_values">
                                            <div class="multi_default_content_item_change_value_prefix">Ξ</div>
                                            <div class="multi_default_content_item_change_value_prefix_val">0</div>
                                        </div>

                                        <div class="multi_default_content_item_fixed_values">
                                            <div class="multi_default_content_item_fixed_value_prefix">$</div>
                                            <div class="multi_default_content_item_fixed_value_prefix_val">0</div>
                                        </div>

                                    </div>

                                    <div class="multi_rh_dc_average_apr multi_default_content_item">

                                        <div class="multi_default_content_item_title">Average APR</div>
                                        <div class="multi_default_content_item_change_values">
                                            <div class="multi_default_content_item_change_value_prefix_val">' . number_format($get_wallet_address_other_info->averageApr, '1') . '%</div>
                                        </div>

                                    </div>

                                    <div class="multi_rh_dc_steth_price multi_default_content_item">

                                        <div class="multi_default_content_item_title">stETH price</div>
                                        <div class="multi_default_content_item_change_values">
                                            <div class="multi_default_content_item_change_value_prefix">$</div>
                                            <div class="multi_default_content_item_change_value_prefix_val">0</div>
                                        </div>

                                        <div class="multi_default_content_item_fixed_values">
                                            <div class="multi_default_content_item_fixed_value_prefix">Ξ</div>
                                            <div class="multi_default_content_item_fixed_value_prefix_val">0</div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="multi_reward_history_result_wrapper">

                            <h4>Stake History</h4>

                            <div class="multi_reward_history_result_table"></div>
                            <div class="multi_reward_spinner"></div>

                        </div>

                    </div>';

                }

            }

            $shortcode_content .= '</div>';

        }

        return $shortcode_content;

    }

    public function get_reward_history_results(){

        global $wpdb;

        if( isset($_POST['wallet_address']) && $_POST['wallet_address'] != "" ){

            // Get wallet address other info
            $wallet_address_other_info = $this->get_wallet_address_other_info($_POST['wallet_address']);

            // Get latest wallet address data

            $get_wallet_data = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'reward_history_wallet_data` WHERE `wallet_address` = "' . $_POST['wallet_address'] . '" ORDER BY `epochDays` DESC');

            if(count($get_wallet_data) > 0){

                echo '<div class="reward_history_default_content_hidden">';
                    echo json_encode(array_merge((array)$get_wallet_data[0], (array)$wallet_address_other_info));
                echo '</div>';

                echo '<table class="row-border">';

                    echo '<thead>';

                        echo '<tr>';

                            echo '<th>Date</th>';
                            echo '<th>Ξ Change</th>';
                            echo '<th>$ Change</th>';
                            echo '<th>Apr</th>';
                            echo '<th>Ξ Balance</th>';

                        echo '</tr>';

                    echo '</thead>';

                    echo '<tbody>';

                        foreach($get_wallet_data as $wallet_data){

                            $epochDays = explode('.', $wallet_data->epochDays);
                            $epochDate = date('Y-m-d', strtotime('1970-01-01 00:00 +' . $epochDays[0] . ' days'));
                            $epochTime = date('H:i', strtotime('1970-01-01 00:00 +' . $epochDays[1] . ' seconds'));

                            echo '<tr>';

                                echo '<td>' . $epochDate . ' ' . $epochTime . '</td>';
                                echo '<td>Ξ ' . number_format(($wallet_data->change) / 1e18, 8) . '</td>';
                                echo '<td>$' . number_format($wallet_data->currencyChange, 2) . '</td>';
                                echo '<td>' . number_format($wallet_data->apr, 1) . '%</td>';
                                echo '<td>Ξ ' . number_format(($wallet_data->balance) / 1e18, 8) . '</td>';

                            echo '</tr>';

                        }

                    echo '</tbody>';

                echo '</table>';

            }

        }

        exit;

    }

    public function get_wallet_address_other_info($wallet_address){

        // sleep(2);

        $reward_history_data = file_get_contents('https://reward-history-backend.lido.fi/?address=' . $wallet_address . '&onlyRewards=true');
        $reward_history_data = json_decode($reward_history_data);

        unset($reward_history_data->events);

        return $reward_history_data;

    }

}

$LidoSTETHRewardHistory = new LidoSTETHRewardHistory();

register_activation_hook ( __FILE__, 'reward_history_on_activate' );

function reward_history_on_activate() {

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $create_reward_history_wallet_address_table = "
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}reward_history_wallet_address` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `wallet_address` text NOT NULL,
            `created_date` datetime NOT NULL,
            PRIMARY KEY id (id)
        ) $charset_collate;
    ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_reward_history_wallet_address_table );

    $create_reward_history_wallet_data_table = "
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}reward_history_wallet_data` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `wallet_address` text NOT NULL,
            `reward_history_id` text NOT NULL,
            `totalPooledEtherBefore` text NOT NULL,
            `totalPooledEtherAfter` text NOT NULL,
            `totalSharesBefore` text NOT NULL,
            `totalSharesAfter` text NOT NULL,
            `block` text NOT NULL,
            `blockTime` text NOT NULL,
            `logIndex` text NOT NULL,
            `type` text NOT NULL,
            `reportShares` text NOT NULL,
            `balance` text NOT NULL,
            `rewards` text NOT NULL,
            `change` text NOT NULL,
            `currencyChange` text NOT NULL,
            `epochDays` text NOT NULL,
            `epochFullDays` text NOT NULL,
            `apr` text NOT NULL,
            PRIMARY KEY id (id)
        ) $charset_collate;
    ";

    dbDelta( $create_reward_history_wallet_data_table );

}

register_deactivation_hook( __FILE__, 'reward_history_on_deactivation' );
 
function reward_history_on_deactivation() {
    wp_clear_scheduled_hook( 'reward_history_cron_hook' );
}