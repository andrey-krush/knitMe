<?php

class Get_Products {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_get_products_route'] );
    }

    public static function register_get_products_route(){
        register_rest_route( 'knitme/v1', '/get_products', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'get_products_callback'],
        ) );
    }

    public static function get_products_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $query_params = $request->get_query_params();

            $args = array(
                'post_type' => 'products',
                'post_status' => 'publish',
                'posts_per_page' => '8'
            );

            if( !empty( $query_params['lang'] ) ) :
                
                $args['lang'] = $query_params['lang'];

            endif;

            if( !empty( $query_params['category'] ) ) : 

                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_category',
                        'field' => 'slug',
                        'terms' => $query_params['category']
                    )
                );

            endif;

            if( !empty( $query_params['orderby'] ) and !empty( $query_params['order'] )  ) : 
                
                $args['order'] = $query_params['order'];
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = $query_params['orderby'];

            endif;

            if( !empty( $query_params['paged'] ) ) :
                
                $args['paged'] = $query_params['paged'];

            endif;

            $query = new WP_Query($args);
            $products = $query->posts;
            $items = array();

            $user = get_user_by_token($token);
            $user_liked_posts = get_user_meta($user->data->ID, 'liked_posts_' . $query_params['lang']);

            foreach( $products as $item ) : 

                $item_data = array(
                    'ID' => $item->ID,
                    'title' => $item->post_title,
                    'subtitle' => get_field('product_info', $item->ID)['subtitle'],
                    'image_url' => get_the_post_thumbnail_url($item->ID),
                );

                if( in_array( $item->ID, $user_liked_posts ) ) : 
                    $item_data['is_liked'] = true;
                else: 
                    $item_data['is_liked'] = false;
                endif;  

                $items[] = $item_data;

            endforeach;

            if( $query->max_num_pages > $query->query_vars['paged'] ) : 
                $next_iteration = $query->query_vars['paged'] + 1;
            else : 
                $next_iteration = null;
            endif;

            $response = new WP_REST_Response(array(
                'next_iteration' => $next_iteration,
                'products' => $items,
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