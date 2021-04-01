<?php
header( "Access-Control-Allow-Origin: *" );
header( "Access-Control-Allow-Methods: POST,GET,PUT,DELETE" );
header( "Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept" );

$file = "../data/posts.json";

// Authorization
if( $_SERVER["REQUEST_METHOD"] != "GET" ){
    if( !isset( $_SESSION["id"] ) ){
        header("HTTP/1.1 401 Unauthorized");
        $response = new stdClass( );
        $response->errors = ["Not logged in."];

        echo json_encode( $response );
    }
}


if( $_SERVER["REQUEST_METHOD"] == "GET" ) {
    
    if( is_file( $file ) ){
        echo file_get_contents( $file );
    } else {
        echo "[]";
    }
}

if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    if( !is_file( $file ) ){
        file_put_contents( $file, "[]" );
    }
    $post = json_decode( file_get_contents( "php://input" ) );

    $posts = json_decode( file_get_contents( $file ) );
    if( !$posts ){
        $posts = [];
    }
    
    array_push( $posts, $post );

    for( $i = 0; $i < count( $posts ); $i++ ){
        $posts[$i]->id = $i+1;
    }

    file_put_contents( $file, json_encode( $posts ) );
}

if( $_SERVER["REQUEST_METHOD"] == "PUT" ) {
    if( is_file( $file ) ){
        $data = json_decode( file_get_contents( "php://input" ) );
    
        $posts = json_decode( file_get_contents( $file ), true );

        $post = $posts[intval( $data->id )];

        foreach ( $data as $key => $value ) {
            $post[$key] = $value;
        }

        $posts[intval( $data->id )] = $post;
        
        file_put_contents( $file, json_encode( $posts ) );
    }
}

if( $_SERVER["REQUEST_METHOD"] == "DELETE" ) {
    if( is_file( $file ) ){
        $post = json_decode( file_get_contents( "php://input" ) );
    
        $posts = json_decode( file_get_contents( $file ) );

        array_splice( $posts, intval( $post->id ), 1 );

        for( $i = 0; $i < count( $posts ); $i++ ){
            $posts[$i]->id = $i+1;
        }

        file_put_contents( $file, json_encode( $posts ) );
    }
}

?>