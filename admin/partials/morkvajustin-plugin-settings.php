<?php
require("functions.php");
mnp_display_nav(); ?>
<div class="container">
	<div class="row">
		<h1>Доставка Justin</h1>
		<?php settings_errors(); ?>
		<hr>
    	<div class="settingsgrid">
			<div class="w70">
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">

					<hr>

						<form method="post" action="options.php">
							<?php
								settings_fields( 'morkvajustin_options_group' );
								do_settings_sections( 'morkvajustin_plugin' );
								submit_button();
							?>
						</form>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		  <?php require 'card.php' ; ?>
		</div>
	</div>
	<?php
		global $wpdb;
		$last_updated_ua = $wpdb->get_results( 'SELECT DISTINCT updated_at FROM ' . $wpdb->prefix . 'woo_justin_ua_cities' );
		$time_ua = ( $last_updated_ua[0]->updated_at ) ?? 0;
		$last_updated_ru = $wpdb->get_results( 'SELECT DISTINCT updated_at FROM ' . $wpdb->prefix . 'woo_justin_ru_cities' );
		$time_ru = ( $last_updated_ru[0]->updated_at ) ?? 0;

		$info_ua = $wpdb->get_results( 'SELECT COUNT(`uuid`) as result  FROM ' . $wpdb->prefix . 'woo_justin_ua_cities');
		$rows_qty_ua = $info_ua[0]->result;
		$info_ru = $wpdb->get_results( 'SELECT COUNT(`uuid`) as result  FROM ' . $wpdb->prefix . 'woo_justin_ru_cities');
		$rows_qty_ru = $info_ua[0]->result;
	 ?>
	<div class="row">
		<h3>Таблиця БД міст українською</h3>
		<?php if ( isset( $_POST['mjs_ua_city_update'] ) ) mrkvjustin_db_cities_update('UA'); ?>
		<span>Останнє оновлення відбулось: <?php echo date("Y-m-d H:i:s", $time_ua); ?> (UTC)</span>
		<form action="admin.php?page=morkvajustin_plugin" method="post" style="display: inline;display: inline-flex;margin-left: 10px;">
			<input type="submit" name="mjs_ua_city_update" value="Оновити UA" class="button">
		</form>
	</div>
	<div class="row">
		<h3>Таблиця БД міст російською</h3>
		<?php if ( isset( $_POST['mjs_ru_city_update'] ) ) mrkvjustin_db_cities_update('RU'); ?>
		<span>Останнє оновлення відбулось: <?php echo date("Y-m-d H:i:s", $time_ru); ?> (UTC)</span>
		<form action="admin.php?page=morkvajustin_plugin" method="post" style="display: inline;display: inline-flex;margin-left: 10px;">
			<input type="submit" name="mjs_ru_city_update" value="Оновити RU" class="button">
		</form>
	</div>
</div>
