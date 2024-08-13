<?php

require_once __DIR__ .'/includes/theme/autoloader.php';
Theme_AutoLoader::init();

add_theme_support('post-thumbnails');

function check_user_by_token( $token ) {

    if( !empty( $token ) ) : 

        $token = base64_decode($token);

        $token = explode(':', $token);

        $user = get_user_by('login', $token[1] );

        if( !empty( $user ) and wp_check_password($token[2], $user->data->user_pass, $user->ID) ) : 
            return true;
        endif;

    endif;

    return false;
}


function add_user_token( $username, $password ) {

    $user = get_user_by('login', $username );
    $secret_key = 'sheepfish';
            
    $user_token = base64_encode( $secret_key . ':' . $username . ':' . $password );
    update_user_meta( $user->data->ID, 'user_token', $user_token );

    return $user_token;
}

function get_user_by_token( $token ) {

    $args = array(
        'meta_key'     => 'user_token',
        'meta_value'   => $token,
        'meta_compare' => '=',
    );
    
    $user_query = new WP_User_Query( $args );
    $user = $user_query->get_results()[0];

    return $user;

}


require_once( get_template_directory() . '/vendor/autoload.php');
\Stripe\Stripe::setApiKey('sk_test_51M5Fq6CsGSzrDG5XlTZ3oXJeDK8NHZnOS8fF6OXjszaZrUqaUBTYkHaXO7oNN47PBcroY0qoR0J1dr5qgM5JptK700TEXN2IwC');
