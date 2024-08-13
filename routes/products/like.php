<?php

class Like {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_like_route'] );
    }

    public static function register_like_route() {
        register_rest_route( 'knitme/v1', '/like', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'like_callback'],
        ) );
    } 

    public static function like_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);


        if( check_user_by_token( $token ) ) :

            $user = get_user_by_token($token);
            $body = json_decode($request->get_body());

            if( !empty( $body->post_id ) ) : 

                $post_language = pll_get_post_language($body->post_id);            

                foreach( pll_languages_list() as $item ) :
                    
                    if( $item != $post_language ) : 

                        $posts[$item] = pll_get_post( $body->post_id, $item );
                    
                    endif;

                endforeach; 

                $posts[$post_language] = $body->post_id;
                
                if( $body->is_liked == true ) :

                    $user_liked_posts = get_user_meta( $user->ID, 'liked_posts_' . $post_language );

                    if( in_array(  $body->post_id, $user_liked_posts ) ) : 

                        $response = new WP_REST_Response(array(
                            'message' => 'post is already liked'
                        ));

                        $response->set_status(400);

                        return $response;

                    endif;

                    foreach( $posts as $key => $item ) : 

                        add_user_meta( $user->data->ID, 'liked_posts_' . $key, $item );

                    endforeach;

                else :

                    foreach( $posts as $key => $item ) : 

                        delete_user_meta( $user->data->ID, 'liked_posts_' . $key, $item );

                    endforeach;

                endif;

                $response = new WP_REST_Response();

                $response->set_status(200);

                return $response;

            else : 

                $response = new WP_REST_Response(array(
                    'message' => 'post_id is not defined'
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