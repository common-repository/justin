<?php

namespace morkva\JustinShip\classes;

if ( ! defined('ABSPATH')) {
  exit;
}

class Activator
{
  public function __construct()
  {
    global $wpdb;
    register_activation_hook(JUSTIN_PLUGENTRY, [ $this, 'activate' ]);
  }

  public function activate()
  {
    global $wpdb;

    $collate = $wpdb->get_charset_collate();
    $base_prefix = $wpdb->base_prefix;

    $wpdb->query("
      CREATE TABLE IF NOT EXISTS `{$base_prefix}woo_justin_ua_cities` (
        uuid VARCHAR(36) NOT NULL,
        descr VARCHAR(255) NOT NULL,
        objectOwner VARCHAR(36),
        updated_at INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (`uuid`)
      ) $collate
    ");

    $wpdb->query("
      CREATE TABLE IF NOT EXISTS `{$base_prefix}woo_justin_ru_cities` (
        uuid VARCHAR(36) NOT NULL,
        descr VARCHAR(255) NOT NULL,
        objectOwner VARCHAR(36),
        updated_at INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (`uuid`)
      ) $collate
    ");

    $wpdb->query("
      CREATE TABLE IF NOT EXISTS `{$base_prefix}woo_justin_ua_warehouses` (
        uuid VARCHAR(36) NOT NULL,
        descr VARCHAR(255) NOT NULL,
        address VARCHAR(255) NOT NULL,
        city_uuid VARCHAR(36),
        branch VARCHAR(18),
        updated_at INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (`uuid`)
      ) $collate
    ");

    $wpdb->query("
      CREATE TABLE IF NOT EXISTS `{$base_prefix}woo_justin_ru_warehouses` (
        uuid VARCHAR(36) NOT NULL,
        descr VARCHAR(255) NOT NULL,
        address VARCHAR(255) NOT NULL,
        city_uuid VARCHAR(36),
        branch VARCHAR(18),
        updated_at INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (`uuid`)
      ) $collate
    ");

    $this->db_cities_update('UA');
    $this->db_cities_update('RU');
    $this->db_warehouses_update('UA');
    $this->db_warehouses_update('RU');

    update_option('woo_justin_migration_version', JUSTIN_PLUGDBV);
  }

  public function db_cities_update($countryCode) // Cities
  {
    global $wpdb;
    $base_prefix = $wpdb->base_prefix;
    $table = $base_prefix . 'woo_justin_' . strtolower($countryCode) . '_cities';
    $updatedAt = time();
    $justinApi = new JustinApiNew();
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

  public function db_warehouses_update($countryCode) // Warehouses
  {
    global $wpdb;
    $base_prefix = $wpdb->base_prefix;
    $table = $base_prefix . 'woo_justin_' . strtolower($countryCode) . '_warehouses';
    $updatedAt = time();
    $justinApi = new JustinApiNew();
    $warehousesJson = $justinApi->getWarehouses($countryCode);
    $warehousesObj = json_decode($warehousesJson);
    $insert = array();
    foreach ($warehousesObj->data as $warehouse) {
        if ($warehouse->fields->Depart->uuid) {
            $insert[] = $wpdb->prepare(
                "('%s', '%s', '%s', '%s', '%s', %d)",
                $warehouse->fields->Depart->uuid,
                $warehouse->fields->descr,
                $warehouse->fields->address,
                $warehouse->fields->city->uuid,
                $warehouse->fields->branch,
                $updatedAt
            );
        }
    }
    $queryInsert = "INSERT INTO $table (`uuid`, `descr`, `address`, `city_uuid`, `branch`, `updated_at`) VALUES ";
    $queryInsert .= implode(",", $insert);
    $queryInsert .= ' ON DUPLICATE KEY UPDATE
        `uuid` = VALUES(`uuid`),
        `descr` = VALUES(`descr`),
        `address` = VALUES(`address`),
        `city_uuid`= VALUES(`city_uuid`),
        `branch`= VALUES(`branch`),
        `updated_at` = VALUES(`updated_at`)';
    $wpdb->query($queryInsert);
  }

}
