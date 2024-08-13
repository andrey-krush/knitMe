<?php 

class Similar_Products {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_similar_products_route'] );
    }

    public static function register_similar_products_route() {
        register_rest_route( 'knitme/v1', '/similar_products', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'similar_products_callback'],
        ) );
    } 

    public static function similar_products_callback( WP_REST_Request $request ) {
        
        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $body = json_decode($request->get_body());

            $image = $body->image;
            $user = get_user_by_token($token);

            if( !empty( $image ) ) : 

                $image = base64_decode($image);
                    
                $products = get_posts(array(
                    'post_type' => 'products',
                    'lang' => $body->language,
                    'posts_per_page' => 6
                ));

                if ( empty( $products )) : 

                    $response = new WP_REST_Response(array(
                        'message' => 'no posts for your request'
                    ));
    
                    $response->set_status(400);
        
                    return $response;

                else : 

                    $user_liked_posts = get_user_meta($user->data->ID, 'liked_posts_' . $body->language);

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

                endif;

                $response = new WP_REST_Response(array(
                    'products' => $items,
                ));
    
                $response->set_status(200);
    
                return $response;


            else : 
                
                $response = new WP_REST_Response(array(
                    'message' => 'image is undefined'
                ));

                $response->set_status(400);
    
                return $response;

            endif;
            
        else:

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }

}