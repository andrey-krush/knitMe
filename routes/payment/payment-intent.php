<?php


class Payment_Intent {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_create_payment_intent_routes'] );
    }

    public static function register_create_payment_intent_routes(){
        register_rest_route( 'knitme/v1', '/create_payment_intent', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'create_payment_intent_callback'],
        ) );
    }

    public static function create_payment_intent_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $stripe = new \Stripe\StripeClient('sk_test_51M5Fq6CsGSzrDG5XlTZ3oXJeDK8NHZnOS8fF6OXjszaZrUqaUBTYkHaXO7oNN47PBcroY0qoR0J1dr5qgM5JptK700TEXN2IwC');

            $user = get_user_by_token($token);
            $customer_id = get_user_meta( $user->data->ID, 'customer_id', true );
            if ( !empty( $customer_id ) ) :
                $intent = $stripe->setupIntents->create([ 'customer' => $customer_id ]);
                $client_secret = $intent->client_secret;
                $response = new WP_REST_Response(array(
                    'client_secret' => $client_secret
                ));
            endif;

            return $response;

        else : 
        
            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;

    } 

}