<?php

class Tariff_Plans {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_tariff_plans_route'] );
    }

    public static function register_tariff_plans_route() {
        register_rest_route( 'knitme/v1', '/tariff_plans', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'tariff_plans_callback'],
        ) );
    }

    public static function tariff_plans_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $tariff_plans_field = get_field('tariff_plans', 'option');

            if( !empty( $tariff_plans_field ) ) : 

                foreach( $tariff_plans_field as $key => $item ) :

                    $item_for_array = array( 
                        'number_of_months' => $item['number_of_months'],
                        'ID' => $key,
                        'regular_price' => $item['regular_price']
                    );
                    
                    if( !empty( $item['sale_price'] ) ) : 
                        $item_for_array['sale_price'] = $item['sale_price']; 
                    endif;


                    $tariff_plans[] = $item_for_array;

                endforeach;

                $response = new WP_REST_Response(array(
                    'tariff_plans' => $tariff_plans
                ));

                $response->set_status(200);

                return $response;

            else : 

                $response = new WP_REST_Response(array(
                    'message' => 'there are no active tariff'
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