/* 
Lido stETH Reward History Plugin Is distributed under GNU2 License
LICENSE

Lido stETH Reward History Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Lido stETH Reward History Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Reward History Plugin.
*/



=== Plugin Name ===
Lido stETH Reward History

== Description ==
Lido stETH Reward History

=== Plugin Version ===
1.0

== Instructions ==
1. Install plugin and activate it.
2. Add wallet addresses from the Admin sidebar menu -> Reward History

== Database Tables ==
* On activation of the plugin two database table will be created with default database prefix

1. reward_history_wallet_address
2. reward_history_wallet_data

== Shortcode ==
* Use [reward_history_all] shortcode anywhere in Page or Post or use <?php echo do_shortcode('[reward_history_all]'); ?> if it requires to execute in specific template file.

== Notes ==
- Daily cron job executes at 23:00 UTC and store data into history table automatically.
