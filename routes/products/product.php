<?php

class Product {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_product_route'] );
    }

    public static function register_product_route(){
        register_rest_route( 'knitme/v1', '/product', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'product_callback'],
        ) );
    }

    public static function product_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token); 
    
        if( check_user_by_token($token) ) : 
        
            $user = get_user_by_token($token);
            $product_id = $request->get_query_params()['post_id'];

            if( !empty( $product_id ) ) : 

                $popularity = get_post_meta( $product_id, 'popularity');

                if( empty( $popularity ) ) : 
                    add_post_meta($product_id, 'popularity', 1, true);
                else :
                    $popularity = $popularity[0] + 1;
                    update_post_meta($product_id, 'popularity', $popularity );
                endif;

                $post_info = get_field('product_info', $product_id);
                
                $post_data = array(
                    'ID' => (int) $product_id,
                    'title' => get_the_title($product_id),
                    'subtitle' => $post_info['subtitle'],
                    'short_description' => $post_info['short_description'],
                    'image' => $post_info['big_image'],
                    'shop_url' => $post_info['shop_url']
                );

                $post_language = pll_get_post_language($product_id);
                $liked_posts = get_user_meta( $user->data->ID, 'liked_posts_' . $post_language );

                if( in_array( $product_id, $liked_posts ) ) : 
                    $post_data['is_liked'] = true;
                else: 
                    $post_data['is_liked'] = false;
                endif;

                $response = new WP_REST_Response(array(
                    'post_data' => $post_data
                ));

                $response->set_status(200);

                return $response;
            
            else : 

                $response = new WP_REST_Response(array(
                    'message' => 'product_id is not defined'
                ));

                $response->set_status(400);

                return $response;

            endif;

        else : 

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }

}