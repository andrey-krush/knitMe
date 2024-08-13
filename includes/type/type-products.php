<?php

class Type_Products {

    public static function init() {
        add_action('init', [ __CLASS__, 'register_products_type']);
        add_action('init', [ __CLASS__, 'register_products_taxonomy']);
    } 

    public static function register_products_type() {

        register_post_type('products', array(
            'labels' => [
                'name'               => 'Products', 
                'singular_name'      => 'Product', 
                'add_new'            => 'Add product', 
                'add_new_item'       => 'Add new product', 
                'edit_item'          => 'Edit product', 
                'new_item'           => 'New product', 
                'view_item'          => 'View product', 
                'search_items'       => 'Search product', 
                'not_found'          => 'Not found', 
                'not_found_in_trash' => 'Not found in trash',
                'parent_item_colon'  => '', 
                'menu_name'          => 'Products',
            ],
            'public' => true,
            'supports' => [ 'title', 'editor', 'thumbnail'],
        ));
    }

    public static function register_products_taxonomy() { 

        register_taxonomy( 'product_category', 'products', array(
            'label'                 => '',
            'labels'                => [
                'name'              => 'Categories',
                'singular_name'     => 'Category',
                'search_items'      => 'Search Categories',
                'all_items'         => 'All Categories',
                'view_item '        => 'View Category',
                'parent_item'       => 'Parent Category',
                'parent_item_colon' => 'Parent Category:',
                'edit_item'         => 'Edit Category',
                'update_item'       => 'Update Category',
                'add_new_item'      => 'Add New Category',
                'new_item_name'     => 'New Category Name',
                'menu_name'         => 'Category',
                'back_to_items'     => '← Back to Category',
            ],
            'public' => true,
            'hierarchical' => true,
        ));

    }

}