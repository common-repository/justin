<?php


 function mnp_display_nav(){//display nav bar

  $arr_of_pages = array(
    array(
      'slug' => 'morkvajustin_plugin', 'label' => 'Налаштування'
    ),
    array(
      'slug' => 'morkvajustin_invoice', 'label' => 'Створити Накладну'
    ),
    array(
      'slug' => 'morkvajustin_invoices', 'label' => 'Мої накладні'
    ),
    array(
      'slug' => 'morkvajustin_about', 'label' => 'Про плагін'
    ),
  );

  echo "<nav class=\"newnaw nav-tab-wrapper woo-nav-tab-wrapper\">";

  $wrs_page = $_GET['page'];

  for($i=0; $i<sizeof($arr_of_pages); $i++){
    $echoclass = 'nav-tab';
    if($wrs_page == $arr_of_pages[$i]['slug']){
      $echoclass = 'nav-tab-active nav-tab';
    }
    echo '<a href=admin.php?page='.$arr_of_pages[$i]['slug'].' class="'.$echoclass.'">'.$arr_of_pages[$i]['label'].'</a>';
  }

  echo "</nav>";
}
function justin_formlinkbox($id){//display top link box
  echo '<div class="alink">';
    if ( !empty( $id ) ) {
      echo '<a class="btn" href="/wp-admin/post.php?post=' . $id . '&action=edit">Повернутись до замовлення</a>';
      echo '';
    }
    echo '<a href="edit.php?post_type=shop_order" >Повернутись до замовлень</a>';
    if ( !empty( $id ) ) {
      echo '<a class="btn" href="admin.php?page=morkvanp_invoice&newttn=1">Накладна без замовлення</a>';
      echo '';
    }


echo '</div>';
}

function mrkvjustin_db_cities_update($countryCode) {
        require 'api.php';
        global $wpdb;
        $table = $wpdb->prefix . 'woo_justin_' . strtolower($countryCode) . '_cities';
        $wpdb->query("TRUNCATE TABLE $table");
        $updatedAt = time();
        $justinApi = new JustinApi();
        $citiesJson = $justinApi->getCity($countryCode);
        $citiesObj = json_decode($citiesJson);

        $insert = array();
        foreach ($citiesObj->data as $city) {
            if ($city->fields->uuid) {
                $insert[] = $wpdb->prepare(
                    "('%s', '%s', '%s', %d)",
                    $city->fields->uuid,
                    $city->fields->descr,
                    $city->fields->objectOwner->uuid,
                    $updatedAt
                );
            }
        }
        $queryInsert = "INSERT INTO $table (`uuid`, `descr`, `objectOwner`, `updated_at`) VALUES ";
        $queryInsert .= implode(",", $insert);
        $queryInsert .= ' ON DUPLICATE KEY UPDATE
            `uuid` = VALUES(`uuid`),
            `descr` = VALUES(`descr`),
            `objectOwner`=VALUES(`objectOwner`),
            `updated_at` = VALUES(`updated_at`)';
        $wpdb->query($queryInsert);
}

?>
