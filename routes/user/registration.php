<?php

class Registration {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_registration_route'] );
    }

    public static function register_registration_route() {

        register_rest_route( 'knitme/v1', '/registration', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'registration_callback'],
        ) );
    }

    public static function registration_callback( WP_REST_Request $request ) {

        $body = json_decode($request->get_body());
        $user = wp_create_user($body->username, $body->password, $body->email);

        if( is_wp_error( $user )  ) :
            
            $error_code = $user->get_error_code();
            $status_code = 403;

            $response = new WP_REST_Response( array(
                'message' => $error_code
            ) );

            $response->set_status($status_code);

            return $response;

        else : 

            $user_token = add_user_token($body->username, $body->password);

            $response = new WP_REST_Response(array(
                'user_token' => $user_token,
                'user_data' => array(
                    'ID' => $user,
                    'username' => $body->username,
                    'email' => $body->email
                )
            ));
            
            $response->set_status(200);
            
            return $response;

        endif;
        
    }

}
