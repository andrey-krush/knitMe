<?php

class Type_Stores {

    public static function init() {
        add_action('init', [ __CLASS__, 'register_stores_type']);
        add_action('init', [ __CLASS__, 'register_stores_taxonomy']);
    } 

    public static function register_stores_type() {

        register_post_type('stores', array(
            'labels' => [
                'name'               => 'Stores', 
                'singular_name'      => 'store', 
                'add_new'            => 'Add store', 
                'add_new_item'       => 'Add new store', 
                'edit_item'          => 'Edit store', 
                'new_item'           => 'New store', 
                'view_item'          => 'View store', 
                'search_items'       => 'Search store', 
                'not_found'          => 'Not found', 
                'not_found_in_trash' => 'Not found in trash',
                'parent_item_colon'  => '', 
                'menu_name'          => 'Stores',
            ],
            'public' => true,
            'supports' => [ 'title', 'thumbnail'],
        ));
    }

    public static function register_stores_taxonomy() { 

        register_taxonomy( 'store_tag', 'stores', array(
            'label'                 => '',
            'labels'                => [
                'name'              => 'Tags',
                'singular_name'     => 'Tag',
                'search_items'      => 'Search Tags',
                'all_items'         => 'All Tags',
                'view_item '        => 'View Tag',
                'parent_item'       => 'Parent Tag',
                'parent_item_colon' => 'Parent Tag:',
                'edit_item'         => 'Edit Tag',
                'update_item'       => 'Update Tag',
                'add_new_item'      => 'Add New Tag',
                'new_item_name'     => 'New Tag Name',
                'menu_name'         => 'Tag',
                'back_to_items'     => '← Back to Tag',
            ],
            'public' => true
        ));

    }

}