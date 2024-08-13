<?php


class Create_Subscription {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_create_subscription_routes'] );
    }

    public static function register_create_subscription_routes(){
        register_rest_route( 'knitme/v1', '/create_subscription', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'create_create_subscription_callback'],
        ) );
    }

    public static function create_create_subscription_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :


            $tariff_plans_field = get_field('tariff_plans', 'option');
            $prices = [];

            foreach ( $tariff_plans_field as $item ) {
                $prices[$item['number_of_months']] = $item['stripe_price'];
            }
            $body = json_decode($request->get_body());

            $user = get_user_by_token($token);
            $customer_id = get_user_meta( $user->data->ID, 'customer_id', true );
            if ( !empty( $customer_id ) ) :
                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer_id,
                    'items' => [
                        ['price' => $prices[ $body->tariff_mounth ]],
                    ],
                    'default_payment_method' => $body->payment_method_id
                ]);
            endif;

            if ( $subscription->status == 'active' ) {
                update_user_meta( $user->data->ID, 'subscription', 'true' );
                update_user_meta( $user->data->ID, 'subscription_id', $subscription->id );
                update_user_meta( $user->data->ID, 'subscription_type', $body->tariff_mounth );
                update_user_meta( $user->data->ID, 'subscription_end', $subscription->current_period_end );

                $response = new WP_REST_Response(array(
                    'status' => 'active'
                ));

                $response->set_status(200);
    
                return $response;
            } else {
                $response = new WP_REST_Response(array(
                    'status' => 'inactive'
                ));

                $response->set_status(400);
    
                return $response;
            }

        else : 
        
            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;

    } 

}