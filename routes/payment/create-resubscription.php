<?php


class Create_Resubscription {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_create_resubscription_routes'] );
    }

    public static function register_create_resubscription_routes(){
        register_rest_route( 'knitme/v1', '/create_resubscription', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__ , 'create_create_resubscription_callback'],
        ) );
    }

    public static function create_create_resubscription_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :


            $tariff_plans_field = get_field('tariff_plans', 'option');
            $prices = [];

            foreach ( $tariff_plans_field as $item ) {
                $prices[$item['number_of_months']] = $item['stripe_price'];
            }

            $user = get_user_by_token($token);
            $customer_id = get_user_meta( $user->data->ID, 'customer_id', true );
            $tariff_mounth = get_user_meta( $user->data->ID, 'subscription_type', true );
            if ( !empty( $customer_id ) ) :
                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer_id,
                    'items' => [
                        ['price' => $prices[ $tariff_mounth ]],
                    ],
                ]);
            endif;

            if ( $subscription->status == 'active' ) {

                update_user_meta( $user->data->ID, 'subscription', 'true' );
                update_user_meta( $user->data->ID, 'subscription_id', $subscription->id );
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