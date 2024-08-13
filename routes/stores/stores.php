<?php

class Stores {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_stores_route'] );
    }

    public static function register_stores_route() {
        register_rest_route( 'knitme/v1', '/stores', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'stores_callback'],
        ) );
    }

    public static function stores_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $query_params = $request->get_query_params();

            $args = array(
                'post_type' => 'stores',
                'post_status' => 'publish',
                'posts_per_page' => '5',
            );

            if( !empty( $query_params['lang'] ) ) : 

                $args['lang'] = $query_params['lang'];

            endif;

            if( !empty( $query_params['paged'] ) ) : 

                $args['paged'] = $query_params['paged'];

            endif;


            if( !empty( $query_params['tags'] ) ) : 

                $tags = explode( ',', $query_params['tags'] );

                $args['tax_query'] = array();

                if( count( $tags ) > 1 ) :
                    $args['tax_query']['relation'] = 'OR';
                endif;

                foreach( $tags as $item ) : 

                    $args['tax_query'][] = array(
                        'taxonomy' => 'store_tag',
                        'field' => 'slug',
                        'terms' => $item
                    );

                endforeach;

            endif;

            $query = new WP_Query($args);
            $stores = $query->posts;

            foreach( $stores as $item ) : 

                $items[] = array(
                    'ID' => $item->ID,
                    'title' => $item->post_title,
                    'short_description' => get_field('shop_info', $item->ID)['short_description'],
                    'shop_url' => get_field('shop_info', $item->ID)['shop_url'],
                    'image_url' => get_the_post_thumbnail_url($item->ID)
                );               
                
            endforeach;

            if( $query->max_num_pages > $query->query_vars['paged'] ) : 

                $next_iteration = $query->query_vars['paged'] + 1;
            
            else : 
        
                $next_iteration = null;
        
            endif;

            $response = new WP_REST_Response(array(
                'items' => $items,
                'next_iteration' => $next_iteration
            ));

            $response->set_status(200);

            return $response;

        else : 
        
            $response = new WP_REST_Response();
    
            $response->set_status(404);
    
            return $response;
    
        endif;
    }

}