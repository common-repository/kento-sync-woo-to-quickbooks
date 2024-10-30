<?php
if ( ! class_exists( 'Kento_Woo_Products', false ) ) {
	/**
	 * Get products details of woocommerce
     *
	 */
	class Kento_Woo_Products {

        function getProductDetails($product_id) {
            $product = wc_get_product( $product_id );
    
            // add data that applies to every product type
            $product_data = $this->get_product_data( $product );
    
            // add variations to variable products
            if ( $product->is_type( 'variable' ) && $product->has_child() ) {
                $product_data['variations'] = $this->get_variation_data( $product );
            }
    
            // add the parent product data to an individual variation
            if ( $product->is_type( 'variation' ) ) {
                $product_data['parent'] = $this->get_product_data( $product->get_parent_id() );
            }
            
            return $product_data;
        }
    
        private function get_product_data( $product ) {
            if ( is_numeric( $product ) ) {
                $product = wc_get_product( $product );
            }
    
            if ( ! is_a( $product, 'WC_Product' ) ) {
                return array();
            }
    
            return array(
                'title'              => $product->get_name(),
                'id'                 => $product->get_id(),
                'created_at'         => $this->format_datetime( $product->get_date_created(), false, true ),
                'updated_at'         => $this->format_datetime( $product->get_date_modified(), false, true ),
                'type'               => $product->get_type(),
                'status'             => $product->get_status(),
                'downloadable'       => $product->is_downloadable(),
                'virtual'            => $product->is_virtual(),
                'permalink'          => $product->get_permalink(),
                'sku'                => $product->get_sku(),
                'price'              => wc_format_decimal( $product->get_price(), 2 ),
                'regular_price'      => wc_format_decimal( $product->get_regular_price(), 2 ),
                'sale_price'         => $product->get_sale_price() ? wc_format_decimal( $product->get_sale_price(), 2 ) : null,
                'taxable'            => $product->is_taxable(),
                'tax_status'         => $product->get_tax_status(),
                'tax_class'          => $product->get_tax_class(),
                'managing_stock'     => $product->managing_stock(),
                'stock_quantity'     => $product->get_stock_quantity(),
                'in_stock'           => $product->is_in_stock(),
                'backorders_allowed' => $product->backorders_allowed(),
                'backordered'        => $product->is_on_backorder(),
                'sold_individually'  => $product->is_sold_individually(),
                'purchaseable'       => $product->is_purchasable(),
                'featured'           => $product->is_featured(),
                'visible'            => $product->is_visible(),
                'catalog_visibility' => $product->get_catalog_visibility(),
                'on_sale'            => $product->is_on_sale(),
                'weight'             => $product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : null,
                'dimensions'         => array(
                    'length' => $product->get_length(),
                    'width'  => $product->get_width(),
                    'height' => $product->get_height(),
                    'unit'   => get_option( 'woocommerce_dimension_unit' ),
                ),
                'shipping_required'  => $product->needs_shipping(),
                'shipping_taxable'   => $product->is_shipping_taxable(),
                'shipping_class'     => $product->get_shipping_class(),
                'shipping_class_id'  => ( 0 !== $product->get_shipping_class_id() ) ? $product->get_shipping_class_id() : null,
                'description'        => apply_filters( 'the_content', $product->get_description() ),
                'short_description'  => apply_filters( 'woocommerce_short_description', $product->get_short_description() ),
                'reviews_allowed'    => $product->get_reviews_allowed(),
                'average_rating'     => wc_format_decimal( $product->get_average_rating(), 2 ),
                'rating_count'       => $product->get_rating_count(),
                'related_ids'        => array_map( 'absint', array_values( wc_get_related_products( $product->get_id() ) ) ),
                'upsell_ids'         => array_map( 'absint', $product->get_upsell_ids() ),
                'cross_sell_ids'     => array_map( 'absint', $product->get_cross_sell_ids() ),
                'categories'         => wc_get_object_terms( $product->get_id(), 'product_cat', 'name' ),
                'tags'               => wc_get_object_terms( $product->get_id(), 'product_tag', 'name' ),
                'featured_src'       => wp_get_attachment_url( get_post_thumbnail_id( $product->get_id() ) ),
                'attributes'         => $this->get_attributes( $product ),
                'download_limit'     => $product->get_download_limit(),
                'download_expiry'    => $product->get_download_expiry(),
                'download_type'      => 'standard',
                'purchase_note'      => apply_filters( 'the_content', $product->get_purchase_note() ),
                'total_sales'        => $product->get_total_sales(),
                'variations'         => array(),
                'parent'             => array(),
            );
        }
    
        private function get_attributes( $product ) {
    
            $attributes = array();
    
            if ( $product->is_type( 'variation' ) ) {
    
                // variation attributes
                foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {
    
                    // taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
                    $attributes[] = array(
                        'name'   => ucwords( str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $attribute_name ) ) ),
                        'option' => $attribute,
                    );
                }
            } else {
    
                foreach ( $product->get_attributes() as $attribute ) {
                    $attributes[] = array(
                        'name'      => ucwords( wc_attribute_taxonomy_slug( $attribute['name'] ) ),
                        'position'  => $attribute['position'],
                        'visible'   => (bool) $attribute['is_visible'],
                        'variation' => (bool) $attribute['is_variation'],
                        'options'   => $this->get_attribute_options( $product->get_id(), $attribute ),
                    );
                }
            }
    
            return $attributes;
        }
        protected function get_attribute_options( $product_id, $attribute ) {
            if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
                return wc_get_product_terms( $product_id, $attribute['name'], array( 'fields' => 'names' ) );
            } elseif ( isset( $attribute['value'] ) ) {
                return array_map( 'trim', explode( '|', $attribute['value'] ) );
            }
    
            return array();
        }
        
        private function get_variation_data( $product ) {
            $variations = array();
    
            foreach ( $product->get_children() as $child_id ) {
                $variation = wc_get_product( $child_id );
    
                if ( ! $variation || ! $variation->exists() ) {
                    continue;
                }
    
                $variations[] = array(
                    'id'                => $variation->get_id(),
                    'created_at'        => $this->format_datetime( $variation->get_date_created(), false, true ),
                    'updated_at'        => $this->format_datetime( $variation->get_date_modified(), false, true ),
                    'downloadable'      => $variation->is_downloadable(),
                    'virtual'           => $variation->is_virtual(),
                    'permalink'         => $variation->get_permalink(),
                    'sku'               => $variation->get_sku(),
                    'price'             => wc_format_decimal( $variation->get_price(), 2 ),
                    'regular_price'     => wc_format_decimal( $variation->get_regular_price(), 2 ),
                    'sale_price'        => $variation->get_sale_price() ? wc_format_decimal( $variation->get_sale_price(), 2 ) : null,
                    'taxable'           => $variation->is_taxable(),
                    'tax_status'        => $variation->get_tax_status(),
                    'tax_class'         => $variation->get_tax_class(),
                    'stock_quantity'    => (int) $variation->get_stock_quantity(),
                    'in_stock'          => $variation->is_in_stock(),
                    'backordered'       => $variation->is_on_backorder(),
                    'purchaseable'      => $variation->is_purchasable(),
                    'visible'           => $variation->variation_is_visible(),
                    'on_sale'           => $variation->is_on_sale(),
                    'weight'            => $variation->get_weight() ? wc_format_decimal( $variation->get_weight(), 2 ) : null,
                    'dimensions'        => array(
                        'length' => $variation->get_length(),
                        'width'  => $variation->get_width(),
                        'height' => $variation->get_height(),
                        'unit'   => get_option( 'woocommerce_dimension_unit' ),
                    ),
                    'shipping_class'    => $variation->get_shipping_class(),
                    'shipping_class_id' => ( 0 !== $variation->get_shipping_class_id() ) ? $variation->get_shipping_class_id() : null,
                    'attributes'        => $this->get_attributes( $variation ),
                    'download_limit'    => (int) $product->get_download_limit(),
                    'download_expiry'   => (int) $product->get_download_expiry(),
                );
            }
    
            return $variations;
        }
    }
}