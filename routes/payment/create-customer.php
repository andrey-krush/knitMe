<?php

use \Stripe\Customer;

class Create_Customer {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_create_customer_routes'] );
    }

    public static function register_create_customer_routes(){
        register_rest_route( 'knitme/v1', '/create_customer', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'create_customer_callback'],
        ) );
    }

    public static function create_customer_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $user = get_user_by_token($token);

            if( !empty( get_user_meta( $user->data->ID, 'customer_id', true ) ) ) : 

                $customer = Customer::create([
                    'email' => $user->data->user_email,
                ]);
                
                update_user_meta($user->data->ID, 'customer_id', $customer->id);

            else : 

                $response = new WP_REST_Response(array(
                    'message' => 'customer_exists'
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