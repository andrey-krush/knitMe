<?php

class Autorization {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_autorization_route'] );
    }

    public static function register_autorization_route() {

        register_rest_route( 'knitme/v1', '/autorization', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'autorization_callback'],
        ) );
    }

    public static function autorization_callback( WP_REST_Request $request ) {

        $body = json_decode($request->get_body());
        $user = get_user_by( 'email', $body->email );

        if( $user and wp_check_password($body->password, $user->data->user_pass, $user->ID) ) : 
            
            $user_token = get_user_meta($user->data->ID, 'user_token' )[0];

            if( empty( $user_token ) ) :
                $user_token = add_user_token($user->data->user_login, $body->password);
            endif;

            $response = new WP_REST_Response(array(
                'user_token' => $user_token,
                'user_data' => array(
                    'ID' => $user->data->ID,
                    'username' => $user->data->user_login,
                    'email' => $body->email
                )
            ));

            $response->set_status(200);

            return $response;

        else : 
           
            $response = new WP_REST_Response();

            $response->set_status(403);

            return $response;

        endif;

        
    }

}
