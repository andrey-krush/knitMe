<?php


class Create_Unsubscribe {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_create_unsubscribe_routes'] );
    }

    public static function register_create_unsubscribe_routes(){
        register_rest_route( 'knitme/v1', '/create_unsubscribe', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'create_create_unsubscribe_callback'],
        ) );
    }

    public static function create_create_unsubscribe_callback( WP_REST_Request $request ) {


        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :
            
            $user = get_user_by_token($token);
            $subscription_id = get_user_meta($user->data->ID,'subscription_id',true);
            $subscription = \Stripe\Subscription::retrieve($subscription_id);
            $subscription->cancel();

            update_user_meta( $user->data->ID, 'subscription', 'false' );
            update_user_meta( $user->data->ID, 'subscription_id', '' );


            $response = new WP_REST_Response();

            $response->set_status(200);

            return $response;

        else : 
        
            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;

    } 

}