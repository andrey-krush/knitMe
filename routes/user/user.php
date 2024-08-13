<?php

class User {

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_user_routes'] );
    }

    public static function register_user_routes(){
        register_rest_route( 'knitme/v1', '/user', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'get_user_callback'],
        ) );

        register_rest_route( 'knitme/v1', '/user', array(
            'methods' => 'DELETE',
            'callback' => [ __CLASS__ , 'delete_user_callback'],
        ) );

        register_rest_route( 'knitme/v1', '/user', array(
            'methods' => 'PUT',
            'callback' => [ __CLASS__ , 'update_user_callback'],
        ) );
    }

    public static function get_user_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $user = get_user_by_token($token);

            $tariff_plans_field = get_field('tariff_plans', 'option');
            $prices = [];

            foreach ( $tariff_plans_field as $item ) {
                $prices[$item['number_of_months']] = $item['sale_price'];
            }

          $subscription_status = get_user_meta( $user->data->ID, 'subscription', true );
          $subscription_plan = get_user_meta( $user->data->ID, 'subscription_type', true );
          $subscription_price = $prices[ $subscription_plan ];
          $subscription_new_data = get_user_meta( $user->data->ID, 'subscription_end', true );;

            $response = new WP_REST_Response(array(
                'token' => $token,
                'user_data' => array(
                    'username' => $user->data->user_login,
                    'email' => $user->data->user_email
                ),
                'subscription_status' => $subscription_status,
                'subscription_plan' =>  $subscription_plan,
                'subscription_price' => $subscription_price ,
                'subscription_new_data' => $subscription_new_data,
            ));

            $response->set_status(200);

            return $response;

        else :

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }

    public static function delete_user_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $user = get_user_by_token($token);

            include ABSPATH . 'wp-admin/includes/user.php';

            wp_delete_user($user->data->ID);

            $response = new WP_REST_Response();

            $response->set_status(200);

            return $response;

        else :

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }

    public static function update_user_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :
            
            $user = get_user_by_token($token);

            $body = json_decode($request->get_body());

            $success_array = array();

            if( isset( $body->pro_plan_status ) ) :

                $pro_plan_status = $body->pro_plan_status;
                $user_pro_plan_status = get_user_meta( $user->data->ID, 'pro_plan_status', true );


                if( !empty( $user_pro_plan_status ) and ( $user_pro_plan_status != $pro_plan_status ) ) : 

                    update_user_meta( $user->data->ID, 'pro_plan_status', $pro_plan_status );

                else : 
                
                    $response = new WP_REST_Response(array(
                        'message' => 'user already has this status'
                    ));

                    $response->set_status(400);

                    return $response;

                endif;

            endif;

            if( isset( $body->auto_renew ) ) :

                $auto_renew = $body->auto_renew;
                $user_auto_renew = get_user_meta( $user->data->ID, 'auto_renew', true );


                if( !empty( $user_auto_renew ) and ( $user_auto_renew != $auto_renew ) ) : 

                    update_user_meta( $user->data->ID, 'auto_renew', $auto_renew );

                else : 
                
                    $response = new WP_REST_Response(array(
                        'message' => 'user already has this status'
                    ));

                    $response->set_status(400);

                    return $response;

                endif;

            endif;

            if( isset( $body->email ) and !empty( $body->email ) ) : 

                $email = $body->email;

                $user_by_email = get_user_by('email', $email);

                    if (empty($user_by_email)) :

                        global $wpdb;

                        $wpdb->update(
                            $wpdb->users,
                            array( 'user_email' => $email ),
                            array( 'ID' => $user->data->ID )
                        );

                    elseif( $user_by_email->data->ID != $user->data->ID ) :

                        $response = new WP_REST_Response(array(
                            'message' => 'user with this email exists'
                        ));

                        $response->set_status(400);

                        return $response;

                    endif; 

            endif;

            if( isset( $body->username ) and !empty( $body->username ) ) : 

                $username = $body->username;

                $user_by_username = get_user_by('login', $username);

                    if (empty($user_by_username)) :

                        global $wpdb;

                        $wpdb->update(
                            $wpdb->users,
                            array( 'user_login' => $username ),
                            array( 'ID' => $user->data->ID )
                        );

                        $token = base64_decode($token);
                        $token = explode(':', $token);

                        $new_token = add_user_token($username, $token[2]);

                        $success_array['user_token'] = $new_token;

                    elseif( $user_by_username->data->ID != $user->data->ID ) :

                        $response = new WP_REST_Response(array(
                            'message' => 'user with this username exists'
                        ));

                        $response->set_status(400);

                        return $response;

                    endif; 
                    
            endif;


            if( !isset( $success_array['user_token'] ) ) :

                $success_array['user_token'] = $token;

            endif;

            $response = new WP_REST_Response($success_array);

            $response->set_status(200);

            return $response;

        else :

            $response = new WP_REST_Response();

            $response->set_status(404);

            return $response;

        endif;
    }

}