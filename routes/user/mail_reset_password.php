<?php

class Mail_Reset_Password {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_mail_reset_password_route'] );
    }

    public static function register_mail_reset_password_route() {
        register_rest_route( 'knitme/v1', '/mail_reset_password', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'mail_reset_password_callback'],
        ) );
    } 

    public static function mail_reset_password_callback( WP_REST_Request $request ) {

        $user_mail = json_decode($request->get_body())->user_mail;
        $user = get_user_by( 'email', $user_mail );

        if( $user ) : 

            $random_number = mt_rand( 10000, 99999 );
                    
            update_user_meta($user->data->ID, 'reset_code', $random_number );

            $reset_code = get_user_meta( $user->data->ID, 'reset_code' )[0];

            $headers = array(
                'From: KnitMe <knit@mail.com>',
                'content-type: text/html',
            );

            $message = 'Your reset code is : ' . $reset_code;

            wp_mail( $user_mail, 'Reset password - KnitMe', $message, $headers  );
        
            $response = new WP_REST_Response();
        
            $response->set_status(200);
            
            return $response;

        else : 
                    
            $response = new WP_REST_Response(array(
                'message' => 'user with such email not exist'
            ));
        
            $response->set_status(400);
            
            return $response;
        
        endif;
            
        
    }

}