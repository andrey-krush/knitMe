<?php

class How_To_Knit { 

    public static function init() { 
        add_action( 'rest_api_init', [ __CLASS__, 'register_how_to_knit_route'] );
    }

    public static function register_how_to_knit_route(){
        register_rest_route( 'knitme/v1', '/how_to_knit', array(
            'methods' => 'GET',
            'callback' => [ __CLASS__ , 'how_to_knit_callback'],
        ) );
    }

    public static function how_to_knit_callback ( WP_REST_Request $request ) {

        $token = $request->get_header('Authorization');
        $token = str_replace('Bearer ', '', $token); 
    
        if( check_user_by_token($token) ) :

            $query_params = $request->get_query_params();
            $post_id = $query_params['post_id'];
            
            $title = get_the_title($post_id);

            $how_to_knit_group = get_field('how_to_knit', $post_id);
            $subtitle = $how_to_knit_group['subtitle'];
            $how_to_knit = $how_to_knit_group['how_to_knit'];
            $big_image = get_field('product_info', $post_id)['big_image'];


            foreach( $how_to_knit as $item ) : 

                if( $item['is_text'] ) : 

                    $how_to_knit_array[] = array(
                        'type' => 'text',
                        'section_title' => $item['section_title'],
                        'text' => $item['text']
                    );

                else : 

                    $how_to_knit_array[] = array(
                        'type' => 'image',
                        'section_title' => $item['section_title'],
                        'image' => $item['image']
                    );

                endif;

            endforeach;

            $response = new WP_REST_Response(array(
                'title' => $title,
                'subtitle' => $subtitle,
                'big_image' => $big_image,
                'how_to_knit' => $how_to_knit_array
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