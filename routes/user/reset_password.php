<?php

class Reset_Password {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_reset_password_route'] );
    }

    public static function register_reset_password_route() {
        register_rest_route( 'knitme/v1', '/reset_password', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'reset_password_callback'],
        ) );
    } 

    public static function reset_password_callback( WP_REST_Request $request ) {

        $body = json_decode($request->get_body());

        $reset_code = $body->reset_code;
        $user_mail = $body->user_mail;
        $password = $body->password;

        $user = get_user_by('email', $user_mail);

        if( $user ) : 

            $user_reset_code = get_user_meta( $user->data->ID, 'reset_code' )[0];

            if( $user_reset_code == $reset_code ) :

                if( !empty( $password ) ) : 
                    wp_set_password( $password, $user->data->ID );
                    add_user_token( $user->data->user_login, $password );
                    delete_metadata('user', $user->data->ID, 'reset_code');

                    $response = new WP_REST_Response();
                
                    $response->set_status(200);
                    
                    return $response;

                else : 

                    $response = new WP_REST_Response(array(
                        'message' => 'pass is empty'
                    ));
                
                    $response->set_status(400);
                    
                    return $response;

                endif;
                
            else : 

                $response = new WP_REST_Response(array(
                    'message' => 'reset code is wrong'
                ));
            
                $response->set_status(403);
                
                return $response;

            endif;
            
        else : 
                    
            $response = new WP_REST_Response(array(
                'message' => 'user with such email not exist'
            ));
        
            $response->set_status(400);
            
            return $response;
        
        endif;

    }

}