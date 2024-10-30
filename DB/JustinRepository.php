<?php

namespace morkva\JustinShip\DB;

use morkva\JustinShip\classes\JustinApiNew;

class JustinRepository
{
  public function getAreas()
  {
    // global $wpdb;

    // return $wpdb->get_results("SELECT * FROM woo_justin_np_areas", ARRAY_A);
  }

  public function getCities($areaRef)
  {


  //   $str = file_get_contents(JUSTIN_PLUGURL.'/classes/json/branches.json');
  //   $json = json_decode($str, true);
  // //  print_r($json['result']);
  //   $options = [
  //   ];

  //     $repository = new JustinRepository();
  //     $cities = $json;
  //     //print_r($cities);
  //     foreach ($cities as $city) {
  //       //echo $city['title_ua'];
  //       $options[$city['title_ua']] = $city['title_ua'];
  //     }

  //     return $options;

    //
    // global $wpdb;
    //
    // return $wpdb->get_results("
    //   SELECT *
    //   FROM woo_justin_np_cities
    //   WHERE area_ref='" . esc_attr($areaRef) . "'
    //   ORDER BY description", ARRAY_A
    // );
  }

  public function getWarehouses($cityRef)
  {
  	$justinApiNew = new JustinApiNew();

  	$countryCode = 'RU';
  	if (  'uk' == get_user_locale() ) $countryCode = 'UA';

  	$apiWarehousesJson = $justinApiNew->getWarehouses( $countryCode );
  	$apiWarehouses = json_decode($apiWarehousesJson, true );
  	$warehouses = isset( $apiWarehouses['data'] ) ? $apiWarehouses['data'] : false;

  	foreach ( $warehouses as $key => $value) {
  		$arr_result['result'][] = array(
  			"adress" 		=> $value['fields']['address'],
  			"locality" 		=> $value['fields']['city']['descr'],
  			"description" 	=> $value['fields']['descr']
  		);
  	}
  	return $arr_result['result'];
  }

  public function getWarehousesDB($cityRef) // Get ukrainian warehouses data from DB
  {
        global $wpdb;
        $warehouses = array();
        $arr_result = array();
        if ( 'uk' == get_user_locale() ) {
            $city_table_name = $wpdb->prefix . 'woo_justin_ua_cities';
            $warehouse_table_name = $wpdb->prefix . 'woo_justin_ua_warehouses';
        }
        if ( 'ru_RU' == get_user_locale() ) {
            $city_table_name = $wpdb->prefix . 'woo_justin_ru_cities';
            $warehouse_table_name = $wpdb->prefix . 'woo_justin_ru_warehouses';
        }
        // Get chosen city uuid
        $city = $wpdb->get_results( "SELECT * FROM {$city_table_name} WHERE descr LIKE '{$cityRef}'", ARRAY_A );
        $city_uuid = $city[0]['uuid'];
        // Get all warehouses of the city
        $warehouses = $wpdb->get_results( "SELECT * FROM {$warehouse_table_name} WHERE city_uuid LIKE '{$city_uuid}' ORDER BY descr ASC", ARRAY_A );
        foreach ( $warehouses as $key => $value) {
          $arr_result['result'][] = array(
              "adress" 		=> $value['address'],
              "description" => $value['descr']
          );
        }
        return $arr_result['result'] ?? false;
  }

  public function saveAreas($areas)
  {
    // global $wpdb;

    // $wpdb->query("TRUNCATE woo_justin_np_areas");

    // foreach ($areas as $area) {
    //   $wpdb->query("
    //     INSERT INTO woo_justin_np_areas (ref, description)
    //     VALUES ('{$area['Ref']}', '" . esc_attr($area['Description']) . "')
    //   ");
    // }
  }

  public function saveCities($cities, $page)
  {
    // global $wpdb;

    // if ($page === 1) {
    //   $wpdb->query("TRUNCATE woo_justin_np_cities");
    // }

    // foreach ($cities as $city) {
    //   $wpdb->query("
    //     INSERT INTO woo_justin_np_cities (ref, description, description_ru, area_ref)
    //     VALUES ('{$city['Ref']}', '" . esc_attr($city['Description']) . "', '" . esc_attr($city['DescriptionRu']) . "', '{$city['Area']}')
    //   ");
    // }
  }

  public function saveWarehouses($warehouses, $page)
  {
    // global $wpdb;

    // if ($page === 1) {
    //   $wpdb->query("TRUNCATE woo_justin_np_warehouses");
    // }

    // foreach ($warehouses as $warehouse) {
    //   $wpdb->query("
    //     INSERT INTO woo_justin_np_warehouses (ref, description, description_ru, city_ref, number)
    //     VALUES ('{$warehouse['Ref']}', '" . esc_attr($warehouse['Description']) . "', '" . esc_attr($warehouse['DescriptionRu']) . "', '{$warehouse['CityRef']}', '" . (int)$warehouse['Number'] . "')
    //   ");
    // }
  }

  public function dropTables()
  {
  	// global $wpdb;

  	// $wpdb->query("DROP TABLE IF EXISTS woo_justin_np_areas");
	  // $wpdb->query("DROP TABLE IF EXISTS woo_justin_np_cities");
	  // $wpdb->query("DROP TABLE IF EXISTS woo_justin_np_warehouses");
  }
}
