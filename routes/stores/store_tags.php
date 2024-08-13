<?php

class Store_Tags {

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_store_tags_route'] );
    }

    public static function register_store_tags_route() {
        register_rest_route( 'knitme/v1', '/store_tags', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'store_tags_callback'],
        ) );
    }

    public static function store_tags_callback( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if( check_user_by_token($token) ) :

            $query_params = $request->get_query_params();
        
            $args = array(
                'taxonomy' => 'store_tag',
                'hide_empty' => true,
                'lang' => $query_params['lang']
            );

            if( isset( $query_params['s'] ) ) : 
                $args['name__like'] = $query_params['s'];
            endif;

            $terms = get_terms($args);

            if( !empty( $terms ) ) : 

                foreach( $terms as $item ) : 
                    
                    $items[] = array(
                        'ID' => $item->term_id,
                        'title' => $item->name,
                        'slug' => $item->slug
                    );

                endforeach;

            else: 
                $items = null;
            endif;

            $response = new WP_REST_Response(array(
                'items' => $items
            ));

            $response->set_status(200);

            return $response;

        else : 
        
            $response = new WP_REST_Response();
        
            $response->set_status(404);
        
            return $response;
        
        endif;
    }

}