<?php

namespace morkva\JustinShip\classes;

if ( ! defined('ABSPATH')) {
  exit;
}

class AssetsLoader
{
  public function __construct()
  {
    add_action('admin_enqueue_scripts', [ $this, 'loadAdminAssets' ]);
    add_action('admin_enqueue_scripts', [ $this, 'injectGlobals' ]);
    add_action('wp_enqueue_scripts', [ $this, 'injectGlobals' ]);
    add_action('admin_enqueue_scripts', [ $this, 'cityAutocomplete' ]);
  }

  public function cityAutocomplete()
  {
    wp_enqueue_script('autocomplete_search', JUSTIN_PLUGURL . 'assets/js/mrkvjs-autocomplete.js',
        ['jquery', 'jquery-ui-autocomplete'], '1.0.0', true);

    global $wpdb;
    if ( 'uk' == get_user_locale() ) $city_table = $wpdb->prefix . 'woo_justin_ua_cities';
    if ( 'ru_RU' == get_user_locale() ) $city_table = $wpdb->prefix . 'woo_justin_ru_cities';
    $cities = $wpdb->get_results( "SELECT uuid, descr FROM {$city_table} ORDER BY descr ASC");

    wp_localize_script('autocomplete_search', 'MrkvjsAutocompleteSearch', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'mrkvjs_city_names' => \json_encode($cities),
            'mrkvjs_nonce' => wp_create_nonce('autocompleteSearchNonce')
        )
    );

    if ( 'uk' == get_user_locale() ) $warehouse_table = $wpdb->prefix . 'woo_justin_ua_warehouses';
    if ( 'ru_RU' == get_user_locale() ) $warehouse_table = $wpdb->prefix . 'woo_justin_ru_warehouses';
    $warehouses = $wpdb->get_results( "SELECT uuid, descr, city_uuid FROM {$warehouse_table} ORDER BY descr ASC");
    wp_localize_script('autocomplete_search', 'MrkvjsAutocompleteDpts', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'mrkvjs_warehouse_names' => \json_encode($warehouses)
        )
    );
  }

  public function loadAdminAssets()
  {
    wp_enqueue_style(
      'woo_justin_admin_css',
      JUSTIN_PLUGURL . 'assets/css/admin.min.css',
      [],
      filemtime(JUSTIN_PLUGFOLDER . 'assets/css/admin.min.css')
    );
  }

  public function injectGlobals()
  {
    $translator = new NPTranslator();
    $translates = $translator->getTranslates();
    /*$requestHandler = get_transient('woo_justin_request_handler');

    if ($requestHandler === false) {
      $requestHandler = 'rest';
    }

    if ($requestHandler === 'rest') {
      $routerScript = 'assets/js/rest-router.js';
    }
    else {
      $routerScript = 'assets/js/ajax-router.js';
    }*/

    $routerScript = 'assets/js/ajax-router.js';

    wp_enqueue_script(
      'woo_justin_router_js',
      JUSTIN_PLUGURL . $routerScript,
      [ 'jquery' ],
      filemtime(JUSTIN_PLUGFOLDER . $routerScript),
      true
    );

    wp_localize_script('woo_justin_router_js', 'woo_justin_globals', [
      'ajaxUrl'                     => admin_url('admin-ajax.php'),
      'homeUrl'                     => home_url(),
      'lang'                        => apply_filters('woo_justin_language', get_option('woo_justin_np_lang', 'ru')),
      'disableDefaultBillingFields' => apply_filters('woo_justin_prevent_disable_default_fields', false) === false ?
        'true' :
        'false',
      'i10n' => [
        'placeholder_area' => $translates['placeholder_area'],
        'placeholder_city' => $translates['placeholder_city'],
        'placeholder_warehouse' => $translates['placeholder_warehouse']
      ]
    ]);
  }
}
