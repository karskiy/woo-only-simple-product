<?php
/*
Plugin Name: Woo Only Simple Product
Plugin URI:  https://github.com/karskiy/woo-only-simple-product
Description: Плагин отключает некоторые функции ВуКоммерс и оставляет минимум, что нужно для одиночного товара в админке при заполнении
Version:     20170417
Author:      Karskiy
Author URI:  https://profiles.wordpress.org/karsky
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bs4inc
Domain Path: /languages
*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  if ( !function_exists( 'krs_woo_simple_product' ) ) {
      function krs_woo_simple_product() {
        // удаляем типы товаров, оставляя в массиве тока один - Одиночный
        add_filter( 'product_type_selector',
          function( $types )
          {
              unset( $types['grouped'] );
              unset( $types['external'] );
              unset( $types['variable'] );

              return $types;
          }
        , 999 );

        // удаляем некоторые табы в области заполнения данных товара
        add_filter( 'woocommerce_product_data_tabs', 
          function( $tabs )
          {
            //unset( $tabs['general'] );
            unset( $tabs['inventory'] );
            unset( $tabs['shipping'] );
            //unset( $tabs['linked_product'] );
            unset( $tabs['attribute'] );
            unset( $tabs['advanced'] );

              return( $tabs );
          }
        , 999 );

        // возвращаем пустой массив, чтоб не было выбора особенностей товара (Скачиваемый, Виртуальный)
        add_filter( 'product_type_options', '__return_empty_array' );


        // врубаем артикул в нужное место
        add_action( 'woocommerce_product_options_pricing', 'krs_add_art', 5 );
        function krs_add_art(){
          // SKU
          if ( wc_product_sku_enabled() ) {
            woocommerce_wp_text_input( array( 'id' => '_sku', 'label' => '<abbr title="'. __( 'Stock Keeping Unit', 'woocommerce' ) .'">' . __( 'SKU', 'woocommerce' ) . '</abbr>', 'desc_tip' => 'true', 'description' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'woocommerce' ) ) );
          } else {
            echo '<input type="hidden" name="_sku" value="' . esc_attr( get_post_meta( $thepostid, '_sku', true ) ) . '" />';
          }
        }
      }
  }
  add_action( 'admin_head', 'krs_woo_simple_product', 999 );
}
