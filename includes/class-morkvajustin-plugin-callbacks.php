<?php
/**
 * Registering callbacks for settings admin page
 */
class MJS_Plugin_Callbacks
{
    public function adminDashboard()
    {
        return require_once("$this->plugin_path/templates/admin.php");
    }

    public function adminInvoice()
    {
        return require_once("$this->plugin_path/templates/invoice.php");
    }

    public function adminSettings()
    {
        return require_once("$this->plugin_path/templates/taxonomy.php");
    }

    public function morkvajustinOptionsGroup($input)
    {
        return $input;
    }

    public function morkvajustinAdminSection()
    {
        echo 'Введіть свій API ключ для початку щоб плагін міг працювати.';
    }

    public function morkvajustingetapi()
    {
        $value = esc_attr(get_option('morkvajustin_apikey'));
        echo '<input type="text"  id="npttnapikey" class="regular-text" name="morkvajustin_apikey" value="' . $value . '" placeholder="API ключ">';
        echo '<p>Якщо у вас немає API ключа, його можна отримати у регіональному представництві JustIn</p>';
    }

    public function morkvajustingetlogin()
    {
        $value = esc_attr(get_option('morkvajustin_login'));
        echo '<input type="text"  id="morkvajustin_login" class="regular-text" name="morkvajustin_login" value="' . $value . '" placeholder="Введіть Ваш логін">';
        // echo '<p>Якщо у вас немає API ключа, його можна отримати у регіональному представництві justin</p>';
    }

    public function morkvajustingetpassword()
    {
        $value = esc_attr(get_option('morkvajustin_password'));
        echo '<input type="password"  id="morkvajustin_password" class="regular-text" name="morkvajustin_password" value="' . $value . '" placeholder="Введіть Ваш пароль">';
        // echo '<p>Якщо у вас немає API ключа, його можна отримати у регіональному представництві justin</p>';
    }

    public function morkvajustinSelectCity()
    {
        $cityName = esc_attr(get_option('city'));

        /**
         * Getting settings of WooShipping plugin
         */
        $shipping_settings = get_option('woocommerce_morkvajustin_shipping_method_settings');
        $cityName = ( ! empty( $shipping_settings["city_name"] ) ) ?? '';

        if (get_option('woocommerce_morkvajustin_shipping_method_city_name')) {
            $cityName = get_option('woocommerce_morkvajustin_shipping_method_city_name');
        }

        echo '<input type="text" class="input-text regular-input  ui-autocomplete-input" name="woocommerce_morkvajustin_shipping_method_city_name" id="woocommerce_morkvajustin_shipping_method_city_name" value="' . $cityName . '" placeholder=" " readonlyd>';

        if (get_option('woocommerce_morkvajustin_shipping_method_city')) {
            $cityid = get_option('woocommerce_morkvajustin_shipping_method_city');
        }
        $cityid = ( ! empty( $cityid ) ) ?? '';
        echo '<input class="input-text regular-input" type="hidden" name="woocommerce_morkvajustin_shipping_method_city" id="woocommerce_morkvajustin_shipping_method_city" style="" value="' . $cityid . '" placeholder="">';
    }

    public function morkvajustinPhone()
    {
        $phone = esc_attr(get_option('justin_phone'));
        echo '<input type="text" class="regular-text" name="justin_phone" value="' . $phone . '" placeholder="380901234567">';
        echo '<p>Підказка: вводьте телефон у таком форматі 380901234567</p>';
    }

    public function morkvajustin_justin_default_price()
    {
        $price = esc_attr(get_option('justin_default_price'));
        echo '<input type="text" class="regular-text" name="justin_default_price" value="' . $price . '" placeholder="">';
        echo '<p>Підказка: вказана ціна доставки буде відображаитсь при оформленні замовлення та буде додана до вартості замовлення</p>';
    }

    public function morkvajustinNames()
    {
        $names = esc_attr(get_option('justin_names'));
        echo '<input type="text" class="regular-text" name="justin_names" value="' . $names . '" placeholder="Петронко Петро Петрович">';
    }

    public function morkvajustinFlat()
    {
        $flat = esc_attr(get_option('flat'));
        echo '<input type="text" class="regular-text" name="flat" value="' . $flat . '" placeholder="номер">';
    }
    public function emptyfunccalbask()
    {
        echo '';
    }
    public function morkvajustinWarehouseAddress()
    {
        $shipping_settings = get_option('woocommerce_morkvajustin_shipping_method_settings');
        $warehouseName = ( ! empty( $shipping_settings["warehouse_name"] ) ) ?? '';

        if (get_option('woocommerce_morkvajustin_shipping_method_warehouse_name')) {
            $warehouseName = get_option('woocommerce_morkvajustin_shipping_method_warehouse_name');
        }

        echo '<input class="input-text regular-input jjs-hide-justin-option" type="text" name="woocommerce_morkvajustin_shipping_method_warehouse_name" id="woocommerce_morkvajustin_shipping_method_warehouse_name" style="" value="' . $warehouseName . '" placeholder="">';

        if (get_option('woocommerce_morkvajustin_shipping_method_warehouse')) {
            $warehouseid = get_option('woocommerce_morkvajustin_shipping_method_warehouse');
        }
        $warehoseid = ( ! empty( $warehouseid ) ) ?? '';
        echo '<input class="input-text regular-input jjs-hide-justin-option" type="hidden" name="woocommerce_morkvajustin_shipping_method_warehouse" id="woocommerce_morkvajustin_shipping_method_warehouse" style="" value="' . $warehouseid . '" placeholder="">';
    }

    public function morkvajustinWarehouseAddress2()
    {
        // $warehouse = esc_attr( get_option( 'warehouse' ) );
        //$shipping_settings = get_option('woocommerce_morkvajustin_shipping_method_settings');
        // $shipping_settings["warehouse_name"];
        //$warehouse = $shipping_settings["warehouse_name"];


        $warehouse = get_option('woocommerce_morkvajustin_shipping_method_address_name');
        $warehouseid = get_option('woocommerce_morkvajustin_shipping_method_address');

        $sender_building = get_option('woocommerce_nova_poshta_sender_building');
        $sender_flat = get_option('woocommerce_nova_poshta_sender_flat');

        echo ' <table class="addressformnpttn" ><tbody><tr>
    <td class="child">
        Вулиця/проспект/мікрорайон
    </td>
    <td>
    <input type="text" placeholder="" class=" input-text regular-input  ui-autocomplete-input" id="woocommerce_morkvajustin_shipping_method_address_name" name="woocommerce_morkvajustin_shipping_method_address_name" value="' . $warehouse . '" placeholder="" readonlyd>
    </td>
    </tr>
    <tr>
    <td><label>Будинок</label>
    </td>
    <td><input type="text" name="woocommerce_nova_poshta_sender_building" value="' . $sender_building . '">
    </td>
    </tr>
    <tr>
    <td>
    <label>Квартира/офіс</label>
    </td>
    <td>
    <input type="text"  name="woocommerce_nova_poshta_sender_flat" value="' . $sender_flat . '">
    </td>
    </tr>
    </tbody>
    </table>';

        echo '<input class="input-text regular-input jjs-hide-justin-option" type="hidden" name="woocommerce_morkvajustin_shipping_method_address" id="woocommerce_morkvajustin_shipping_method_address" style="" value="' . $warehouseid . '" placeholder="">';

        ///echo '<p>Налаштування полей міста і регіона беруться із налаштувань плагіну <a href="admin.php?page=wc-settings&tab=shipping&section=morkvajustin_shipping_method">Woocommerce</a></p>';
    }

    public function morkvajustinInvoiceWeight()
    {
        $activate = get_option('invoice_weight');
        $checked = $activate;
        $current = 1;
        $echo = false;
        echo '<input type="checkbox" class="regular-text" name="invoice_weight" value="1" ' . checked($checked, $current, $echo) . ' />';
    }

    public function morkvajustinInvoiceautottn()
    {
        $activate = get_option('autoinvoice');

        $checked = $activate;
        $current = 1;
        $echo = false;
        echo '<input ' . $activate . ' type="checkbox" class="regular-text" name="autoinvoice" value="1" ' . checked($checked, $current, $echo) . ' /><p>Накладні, за можливості, формуватимуться автоматично при оформленні замовлення. <br><strong style="color:#a55">Функція ще в процесі тестування, тому перевіряйте правильність створення накладних за <a href=admin.php?page=morkvajustin_about#test>посиланням</a></strong> </p>';
    }

    public function morkvajustincalc()
    {
        $activate = get_option('show_calc');

        $checked = $activate;
        $current = 1;
        $echo = false;
        echo '<input ' . $activate . ' type="checkbox" class="regular-text" name="show_calc" value="1" ' . checked($checked, $current, $echo) . ' />Сума доставки не включається у замовлення за замовчуванням, хоч і відображається у настройках. </p>';
    }

    public function morkvajustinInvoiceshort()
    {
        $activate = get_option('invoice_short');

        $checked = $activate;
        $current = 1;
        $echo = false;
        echo '<input ' . $activate . ' type="checkbox" class="regular-text" name="invoice_short" value="1" ' . checked($checked, $current, $echo) . ' /><p>якщо увімкнено, функціонал плагіна розширюється можливістю використовувати шорткоди</p>';
    }

    public function morkvajustinInvoicepayer()
    {
        $value = get_option('justin_invoice_payer');

        $values = array(
            '0',
            '1'
        );
        $volues = array(
            'Отримувач',
            'Відправник'
        );
        $vilues = array(
            '',
            ''
        );
        for ($i = 0;$i < sizeof($values);$i++) {
            if ($values[$i] == $value) {
                $vilues[$i] = 'selected';
            }
        }

        echo '<select ' . $value . ' id="justin_invoice_payer" name="justin_invoice_payer">
		<p> </p>';

        for ($i = 0;$i < sizeof($values);$i++) {
            echo '<option ' . $vilues[$i] . ' value="' . $values[$i] . '">' . $volues[$i] . '</option>';
        }

        echo '</select>';
    }

    public function morkvajustinInvoicecron()
    {
        $invoice_dpay = get_option('invoice_cron');

        ///	$crontime = intval($invoice_dpay);
        $textt = '';

        if ($invoice_dpay) {
            $textt = 'Крон вимкнуто. Якщо не бажаєте оновлювати статуси автоматично, позначте пункт';
        } else {
            $textt = 'Крон завдання відбуватиметься щогодинно.';
        }

        $echo = false;
        echo '<input value="' . $invoice_dpay . '" type="checkbox" class="regular-text" name="invoice_cron" value="55"  /><p>';
    }

    public function morkvajustinInvoiceauto()
    {
        $checked = get_option('np_invoice_auto_ttn');
        $current = 1;
        $echo = false;
        echo '<input ' . $checked . ' type="checkbox" class="regular-text" name="np_invoice_auto_ttn"  value="1" ' . checked($checked, $current, $echo) . ' /><p>
    Щогодинний крон оновлення статусів замовлень.</p>';
    }

    public function morkvajustinInvoicecpay()
    {
        $activate = get_option('invoice_cpay');

        $checked = $activate;
        $current = 1;
        $echo = false;
        echo '<input ' . $activate . ' type="checkbox" class="regular-text" name="invoice_cpay" value="1" ' . checked($checked, $current, $echo) . '" /><p><b>Контроль платежу доступний тільки для юридичних осіб які уклали таку угоду з НП.</b> <br>
		Гроші за "наложку" зараховуються на рахунок компанії і оподатковуються.<br> Якщо увімкнено, функціонал плагіна розширюється можливістю Формування запиту на створення «ЕН» з послугою «Контроль оплати» </p>';
    }

    public function morkvajustinEmailTemplate()
    {
        $content = get_option('morkvajustin_email_template');
        $editor_id = 'morkvajustin_email_editor_id';
        wp_editor($content, $editor_id, array(
            'textarea_name' => 'morkvajustin_email_template',
            'tinymce' => 0,
            'media_buttons' => 0
        ));

        echo '<span id=standarttext title="щоб встановити шаблонний текст, натисніть">Шаблон email</span>';
    }

}
