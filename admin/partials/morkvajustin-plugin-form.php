<?php
require_once("functions.php");
require_once 'api.php';

// Get site current language
if ( 'uk' == get_user_locale() ) $countryCode = 'UA';
if ( 'ru_RU' == get_user_locale() ) $countryCode = 'RU';
$api_justin_invoice_query = false; // Http-query to API JustIn is not exectuted yet

// Sender data
global $wpdb;
$sender_city_name = get_option('woocommerce_morkvajustin_shipping_method_city_name');
$sender_warehouse_name = get_option('woocommerce_morkvajustin_shipping_method_warehouse_name');
$sender_city_uuid = $_POST['invoice_sender_city_uuid'] ?? get_option('woocommerce_morkvajustin_shipping_method_city');
$sender_warehouse_uuid = $_POST['woocommerce_morkvajustin_shipping_method_warehouse'] ?? get_option('woocommerce_morkvajustin_shipping_method_warehouse');
$city_table_name = $wpdb->prefix . 'woo_justin_' . strtolower( $countryCode ) . '_cities';
$warehouse_table_name = $wpdb->prefix . 'woo_justin_' . strtolower( $countryCode ) . '_warehouses';
$sender_warehouse_branch_arr = $wpdb->get_col( "SELECT branch FROM {$warehouse_table_name} WHERE uuid = '{$sender_warehouse_uuid}'" );
$phone_bad_symbols = array( '+', '-', '(', ')', ' ' );
$sender_phone = str_replace( $phone_bad_symbols, '', get_option( 'justin_phone' ) );
$justin_invoice_payer = get_option( 'justin_invoice_payer' ); // 0 - Одержувач, 1 - Відправник

$justinapi = new JustinApi();

mnp_display_nav();

// Get current order data
if (!isset($_SESSION)) {
    session_start();
}

// Set order id if  HTTP REFFERRER  is woocommerce order
if (isset($_SERVER['HTTP_REFERER'])) {
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if (!empty($qs)) {
        parse_str($qs, $output);
        // TODO check for key existence
        if (isset($output['post'])) {
            $order_id =  $output['post'];  // id
        }
    }
}

// If isset order from previous step id and not null srialize order id to session
// Else do  not show ttn form
if (isset($order_id) && ($order_id != 0) &&  wc_get_order($order_id)) {
    $order_data0 = wc_get_order($order_id);
    if (isset($order_data0) && (!$order_data0 == false)) {
        $order_data = $order_data0->get_data();
        $_SESSION['order_id'] = serialize($order_id);
    } else {
        $showpage =false;
        wp_die('<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку Замовлення</a></h3>');
    }
}

// If isset order id only from session  get it
elseif (isset($_SESSION['order_id'])) {
    //$order_id = 0;
    $ret = @unserialize($_SESSION['order_id']);
    if (gettype($ret) == 'boolean') {
        $order_id = $_SESSION['order_id'];
    } else {
        $order_id = unserialize($_SESSION['order_id']);
    }
    if (wc_get_order($order_id)) {
        $order_data0 = wc_get_order($order_id);
        $order_data = $order_data0->get_data();
    }
}

// Else do not show form ttn
else {
    $showpage =false;
    wp_die('<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку Замовлення</a></h3>');
}

if ( ! isset( $order_data['id'] ) ) {
    $order_data = [
        'id'      => null,
        'billing' => [
            'first_name' => '',
            'last_name'  => '',
            'city'       => '',
            'phone'      => '',
            'address_1'  => ''
        ]
    ];
}

// Recipient data
$recipient_city_name = $order_data['billing']['city'];
$recipient_city_uuid_arr = $wpdb->get_col( "SELECT uuid FROM {$city_table_name} WHERE descr = '{$recipient_city_name}'" );
$recipient_warehouse_name = $order_data['billing']['address_1'];
$recipient_warehouse_uuid_arr = $wpdb->get_col( "SELECT uuid FROM {$warehouse_table_name} WHERE descr = '{$recipient_warehouse_name}'" );
$recipient_warehouse_branch_arr = $wpdb->get_col( "SELECT branch FROM {$warehouse_table_name} WHERE uuid = '{$recipient_warehouse_uuid_arr[0]}'" );
$recipient_phone = str_replace( $phone_bad_symbols, '', $order_data['billing']['phone'] ) ?? '';
$order_total_price = $order_data['total']; // Повна вартість замовлення

// After 'Створити' button clicked
if ( ! empty( $_POST['mrkvjs_create_ttn'] ) ) {

    // Create array with future invoice data to sabe in DB
    $invoice_db_data = array();
    $city_sender = $wpdb->get_row( // Get city uuid from UA or RU DB name cities table
        "
        SELECT uuid
        FROM {$city_table_name}
        WHERE descr = '{$_POST['invoice_sender_city']}'
        "
    );
    $invoice_db_data = array( // Create invoice data for DB
        'order_id' => $order_id,
        'order_invoice' => '',
        'invoice_ref' => ''
    );

    // Create data array for invoice API Justin query
    require 'create_senddata.php';

    // Create Justin invoice for current order in API Justin
    $justinApiTtnObj = $justinapi->createTtn( get_option( 'morkvajustin_apikey' ), $senddata );
    $justinApiTtn = json_decode( $justinApiTtnObj );

   if ( 'success' == $justinApiTtn->result ) {

       $order_invoice_number = $justinApiTtn->data->number;
       $order_invoice_ref = $justinApiTtn->data->ttn;

       // Add data to $invoice_db_data array from API JustIn response
       $invoice_db_data['order_invoice'] = $order_invoice_number;
       $invoice_db_data['invoice_ref'] = $order_invoice_ref;

       // Insert invoice data row in DB invoice table
       $invoice_table_name = $wpdb->prefix . 'justin_ttn_invoices';
       $wpdb->insert( $invoice_table_name, $invoice_db_data, ['%d', '%d', '%s'] );

       // Create custom field 'justin_ttn' for 'Редагувати замовлення' page
       $order = wc_get_order( $order_data["id"] );
       $meta_invoice_number_key = 'justin_ttn';
       $meta_invoice_number_value = $justinApiTtn->data->number;
       update_post_meta ($order_id, $meta_invoice_number_key, $meta_invoice_number_value );

       // Set message in sidebar 'Замовлення Приміток' on 'Редагувати замовлення' page
       $note = "Відправлення JustIn: " . $meta_invoice_number_value .".";
       $order->add_order_note($note);
       $order->save();
       $api_justin_invoice_query = true;

   } elseif ( 'error' == $justinApiTtn->result ) {

       $api_justin_invoice_query = false;
       $justinApiTtnError = is_object( $justinApiTtn->errors[0] ) ? $justinApiTtn->errors[0]->error : $justinApiTtn->errors[0];
       if ( 'Sender city ID not correct' == $justinApiTtnError ) {
           $justinApiTtnError .= '.<br>Спробуйте перевизначити місто і відділення Відправника в налаштуваннях плагіну';
       }
       echo '<div id="mrkvjs_err_msg" style="margin-left:0;margin-right: 0;height:95px;padding:8px;border-left:4px solid #ce4a36;margin-top:20px;width:65%;background:#fff;">
               <div id="messagebox-err" style="border-left-color:#ce4a36;">
                     <h3 style="margin-left: 10px;">Відправлення не створене.</h3>
                     <p style="margin-left: 10px;">
                        <span style="color:#ce4a36;">Помилка від API Justin: ' . $justinApiTtnError .  '.</span><br>
                     </p>
               </div>
            </div>';
   }
}
?>

<?php // 'Створити накладну' html-template ?>
<h1 style="font-size:23px;font-weight:400;line-height:1.3;"><?php echo 'Нове відправлення JustIn №' . $order_data['id']; ?></h1>
<div class="">
   <div class="container">
      <form class="form-invoice form-invoice-3cols"  method="post" name="invoice">
         <?php  if ( $api_justin_invoice_query ) { ?>
             <div id="messagebox" class="messagebox_show updated" data="165" style="height:0;padding:0">
                 <div class="sucsess-naklandna">
                    <h3>Відправлення <?php echo $meta_invoice_number_value; ?> успішно створене!</h3>
					<p>
						Тип відправлення: <?php $delivery_type = ( 0 == $senddata['delivery_type']) ? 'B2C' : 'Не визначено'; echo $delivery_type; ?><br>
                        Відправник: <?php echo $senddata['sender_company']; ?></br>
                        Адреса відправлення: <?php echo $sender_warehouse_name . ', ' . $sender_city_name; ?><br>
                        Одержувач: <?php echo $senddata['receiver']; ?></br>
                        Адреса отримання: <?php echo $recipient_warehouse_name . ', ' . $recipient_city_name; ?><br>
                        <span style="color:#ce4a36;font-weight:600;">Перевірте правильність створеної накладної в особистому кабінеті!</span>
					</p>
				</div>
             </div>
         <?php } ?>
         <?php  justin_formlinkbox($order_data['id']); ?>
         <div class="tablecontainer">
            <table class="form-table full-width-input">
               <tbody id="tb1">
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Відправник</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_name">Відправник (П. І. Б)</label>
                     </th>
                     <td>
                      <input style="display:text" type="text" id="sender_name" name="invoice_sender_name" class="input sender_name" value="<?php echo get_option('justin_names'); ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_namecity">Місто</label>
                     </th>
                     <td>
                        <input id="sender_namecity" type="text" value="<?php echo $sender_city_name; ?>" readonly="" name="invoice_sender_city">
                        <input type="hidden" name="invoice_sender_city_uuid" value="<?php echo $sender_city_uuid; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_phone">Телефон</label>
                     </th>
                     <td>
                        <input type="text" id="sender_phone" name="invoice_sender_phone" class="input sender_phone" value="<?php echo sanitize_text_field( $sender_phone ); ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_description">Опис відправлення</label>
                     </th>
                     <td class="pb7">
                        <textarea type="text" id="invoice_description" name="invoice_description" class="input" minlength="1" required=""><?php echo get_option('justin_invoice_description'); ?></textarea>
                        <p id="error_dec"></p>
                     </td>
                  </tr>
               </tbody>
            </table>
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Одержувач</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_name">Одержувач (П.І.Б)</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_name" id="recipient_name" class="input" recipient_name="" value="<?php echo $order_data['billing']['first_name']." ".$order_data['billing']['last_name']; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_city">Місто одержувача</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_city" id="recipient_city" class="recipient_city" value="<?php echo $order_data['billing']['city']; ?>">
                        <input type="hidden" name="invoice_recipient_city_uuid" value="<?php echo $recipient_city_uuid_arr[0]; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="RecipientAddressName">відділення:</label>
                     </th>
                     <td>
                        <textarea name="addresstext"><?php echo $order_data['billing']['address_1']; ?></textarea>
                        <input type="hidden" name="invoice_recipient_warehouse_uuid" value="<?php echo $recipient_warehouse_uuid_arr[0]; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_phone">Телефон</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_phone" class="input recipient_phone" id="recipient_phone" value="<?php echo sanitize_text_field( $recipient_phone ); ?>">
                     </td>
                  </tr>
               </tbody>
            </table>

         </div>
         <div class="tablecontainer">
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Параметри відправлення</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row"><label>Запланована дата:</label></th>
                     <?php $today = date('Y-m-d'); ?>
                     <td><input type="date" name="invoice_datetime" value="<?php echo $today; ?>" min="<?php echo $today; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_payer">Платник послуг</label>
                     </th>
                    <td>
                        <select id="invoice_payer" name="invoice_payer">
                           <?php if ( '0' === $justin_invoice_payer ) : ?>
        		               <option value="Recipient" selected="">Отримувач</option>
        		               <option value="Sender">Відправник</option>
                           <?php elseif ( '1' === $justin_invoice_payer ) : ?>
                               <option value="Sender" selected="">Відправник</option>
                               <option value="Recipient">Отримувач</option>
                           <?php endif; ?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                     <th scope="row"><label class="light" for="invoice_cargo_mass">Вага, кг</label></th>
                     <td><input type="text" name="invoice_cargo_mass" id="invoice_cargo_mass" value="1.2">
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                       <p class="light">Якщо залишити порожнім, буде використано мінімальне значення 0.5.</p>
                     </td>
                  </tr>
                  <tr>
                     <td class="pb0">
                        <label class="light" for="invoice_volumei">Об'єм, м<sup>3</sup></label>
                     </td>
                     <td class="pb0">
                        <input type="text" id="invoice_volumei" name="invoice_volume" value="0">
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <p></p>
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_placesi">Кількість місць</label>
                     </th>
                     <td>
                        <input type="text" id="invoice_placesi" name="invoice_places" value="1">
                     </td>
                  </tr>
                  <input type="hidden" name="InfoRegClientBarcodes" value="13812">
                  <tr>
                     <th scope="row">
                        <label for="invoice_priceid">Оголошена вартість</label>
                     </th>
                     <td>
                        <input id="invoice_priceid" type="text" name="invoice_price" required="" value="<?php echo $order_total_price ?>">
                     </td>
                  </tr>
                  <tr>
                     <th colspan="2">
                        <p class="light">Якщо залишити порожнім, плагін використає вартість замовлення</p>
                     </th>
                  </tr>
               </tbody>
            </table>
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <td>
                        <input name="mrkvjs_create_ttn" type="submit" value="Створити" class="checkforminputs button button-primary" id="submit">
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div>
            <?php require 'card.php' ; ?>
            <div class="clear"></div>
         </div>
      </form>
   </div>
</div>
