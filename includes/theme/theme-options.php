<?php 

class Theme_Options {

    public static function init() {

        add_action('acf/init', [__CLASS__, 'options_pages'] );
        add_action('acf/init', [__CLASS__, 'acf_add_local_field_group'] );

    }

    public static function options_pages() {

        if ( function_exists( 'acf_add_options_page' ) ) {
        
            // $parent = acf_add_options_page( array(
            // 'page_title' => 'Theme General Settings',
            // 'menu_title' => 'Theme Settings',
            // 'redirect'   => 'Theme Settings',
            // ) );
    
            // $languages = pll_languages_list();
    
            // foreach ( $languages as $lang ) {
            //     acf_add_options_sub_page( array(
            //     'page_title' => 'Option page ' . $lang,
            //     'menu_title' => 'Option page ' . $lang,
            //     'menu_slug'  => 'options_' . $lang,
            //     'post_id'    => $lang,
            //     'parent'     => $parent['menu_slug']
            //     ) );
            // }

            $option_page = acf_add_options_page(array(
                'page_title'    => 'Theme General Settings',
                'menu_title'    => 'Theme Settings',
                'menu_slug'     => 'theme-general-settings',
                'capability'    => 'edit_posts',
                'redirect'      => false
            ));
    
        }
    }

    public static function acf_add_local_field_group(){

        // $languages = pll_languages_list();
        // $locations = array();
        // foreach( $languages as $lang ) :

        //     $locations[] = array(
        //         array(
        //             'param' => 'options_page',
        //             'operator' => '==',
        //             'value' => 'options_' . $lang,
        //         ),
        //     );

        // endforeach;

        // if ( function_exists('acf_add_local_field_group') ):

        //     acf_add_local_field_group(array(
        //         'key' => 'group_64998e57b48ae',
        //         'title' => 'Options page',
        //         'fields' => array(
        //             array(
        //                 'key' => 'field_649993cf0a324',
        //                 'label' => 'Tariff Plans',
        //                 'name' => '',
        //                 'aria-label' => '',
        //                 'type' => 'tab',
        //                 'instructions' => '',
        //                 'required' => 0,
        //                 'conditional_logic' => 0,
        //                 'wrapper' => array(
        //                     'width' => '',
        //                     'class' => '',
        //                     'id' => '',
        //                 ),
        //                 'placement' => 'top',
        //                 'endpoint' => 0,
        //             ),
        //             array(
        //                 'key' => 'field_64998e582cda5',
        //                 'label' => '',
        //                 'name' => 'tariff_plans',
        //                 'aria-label' => '',
        //                 'type' => 'repeater',
        //                 'instructions' => '',
        //                 'required' => 0,
        //                 'conditional_logic' => 0,
        //                 'wrapper' => array(
        //                     'width' => '',
        //                     'class' => '',
        //                     'id' => '',
        //                 ),
        //                 'layout' => 'table',
        //                 'pagination' => 0,
        //                 'min' => 0,
        //                 'max' => 0,
        //                 'collapsed' => '',
        //                 'button_label' => 'Add Row',
        //                 'rows_per_page' => 20,
        //                 'sub_fields' => array(
        //                     array(
        //                         'key' => 'field_649994070a325',
        //                         'label' => 'Number of months',
        //                         'name' => 'number_of_months',
        //                         'aria-label' => '',
        //                         'type' => 'text',
        //                         'instructions' => '',
        //                         'required' => 0,
        //                         'conditional_logic' => 0,
        //                         'wrapper' => array(
        //                             'width' => '',
        //                             'class' => '',
        //                             'id' => '',
        //                         ),
        //                         'default_value' => '',
        //                         'translations' => 'translate',
        //                         'maxlength' => '',
        //                         'placeholder' => '',
        //                         'prepend' => '',
        //                         'append' => '',
        //                         'parent_repeater' => 'field_64998e582cda5',
        //                     ),
        //                     array(
        //                         'key' => 'field_649994180a326',
        //                         'label' => 'Regular price',
        //                         'name' => 'regular_price',
        //                         'aria-label' => '',
        //                         'type' => 'number',
        //                         'instructions' => '',
        //                         'required' => 0,
        //                         'conditional_logic' => 0,
        //                         'wrapper' => array(
        //                             'width' => '',
        //                             'class' => '',
        //                             'id' => '',
        //                         ),
        //                         'default_value' => '',
        //                         'translations' => 'copy_once',
        //                         'min' => '',
        //                         'max' => '',
        //                         'placeholder' => '',
        //                         'step' => '',
        //                         'prepend' => '',
        //                         'append' => '',
        //                         'parent_repeater' => 'field_64998e582cda5',
        //                     ),
        //                     array(
        //                         'key' => 'field_649994420a327',
        //                         'label' => 'Sale price',
        //                         'name' => 'sale_price',
        //                         'aria-label' => '',
        //                         'type' => 'number',
        //                         'instructions' => '',
        //                         'required' => 0,
        //                         'conditional_logic' => 0,
        //                         'wrapper' => array(
        //                             'width' => '',
        //                             'class' => '',
        //                             'id' => '',
        //                         ),
        //                         'default_value' => '',
        //                         'translations' => 'copy_once',
        //                         'min' => '',
        //                         'max' => '',
        //                         'placeholder' => '',
        //                         'step' => '',
        //                         'prepend' => '',
        //                         'append' => '',
        //                         'parent_repeater' => 'field_64998e582cda5',
        //                     ),
        //                 ),
        //             ),
        //         ),
        //         'location' => $locations,
        //         'menu_order' => 0,
        //         'position' => 'normal',
        //         'style' => 'default',
        //         'label_placement' => 'top',
        //         'instruction_placement' => 'label',
        //         'hide_on_screen' => '',
        //         'active' => true,
        //         'description' => '',
        //         'show_in_rest' => 0,
        //     ));
            
        // endif;            

    }

}