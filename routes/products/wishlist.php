<?php

class Wishlist {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_wishlist_route'] );
    }

    public static function register_wishlist_route() {
        register_rest_route( 'knitme/v1', '/wishlist', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'wishlist_callback'],
        ) );
    } 

    public static function wishlist_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $user = get_user_by_token($token);
            $query_params = $request->get_query_params();

            $iteration = $query_params['iteration'];
            $language = $query_params['language'];
            
            if( $iteration == 1 ) : 

                $start_range = 0;

            else :

                $start_range = ($iteration - 1) * 8;     

            endif;

            $end_range = $iteration * 8 - 1;

            $liked_posts = get_user_meta( $user->data->ID, 'liked_posts_' . $language );
            
            if( isset( $liked_posts[$end_range + 1] ) ) : 
                $next_iteration = $iteration + 1;
            else: 
                $next_iteration = false;
            endif;

            if( !empty( $liked_posts ) ) :

                $liked_posts = array_slice( $liked_posts, $start_range, $end_range );

                foreach( $liked_posts as $item ) : 

                    $item_data = array(
                        'ID' => (int) $item,
                        'title' => get_the_title($item),
                        'image_url' => get_the_post_thumbnail_url($item),
                        'subtitle' => get_field('product_info', $item)['subtitle'],
                    ); 

                    $items[] = $item_data;

                endforeach;

            else : 

                $liked_posts = null;

            endif; 

            $response = new WP_REST_Response(array(
                'items' => $items,
                'next_iteration' => $next_iteration,
            ));

            $response->set_status(200);

            return $response;

        else:

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }
}