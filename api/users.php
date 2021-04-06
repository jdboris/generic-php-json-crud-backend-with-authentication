<?php
header( "Access-Control-Allow-Origin: http://localhost:5501" );
header( "Access-Control-Allow-Credentials: true" );
header( "Access-Control-Allow-Methods: GET, POST, DELETE, PUT" );
header( "Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept" );

session_set_cookie_params( ["samesite" => "None", "secure" => "true"] );
session_start( );

$file = "../data/users.json";

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
    $item = json_decode( file_get_contents( "php://input" ) );

    $items = json_decode( file_get_contents( $file ) );
    if( !$items ){
        $items = [];
    }
    
    array_push( $items, $item );

    for( $i = 0; $i < count( $items ); $i++ ){
        $items[$i]->id = $i+1;
    }

    file_put_contents( $file, json_encode( $items ) );
}

if( $_SERVER["REQUEST_METHOD"] == "PUT" ) {
    if( is_file( $file ) ){
        $data = json_decode( file_get_contents( "php://input" ) );
    
        $items = json_decode( file_get_contents( $file ), true );

        $item = $items[intval( $data->id )];

        foreach ( $data as $key => $value ) {
            $item[$key] = $value;
        }

        $items[intval( $data->id )] = $item;
        
        file_put_contents( $file, json_encode( $items ) );
    }
}

if( $_SERVER["REQUEST_METHOD"] == "DELETE" ) {
    if( is_file( $file ) ){
        $item = json_decode( file_get_contents( "php://input" ) );
    
        $items = json_decode( file_get_contents( $file ) );

        array_splice( $items, intval( $item->id ), 1 );

        for( $i = 0; $i < count( $items ); $i++ ){
            $items[$i]->id = $i+1;
        }

        file_put_contents( $file, json_encode( $items ) );
    }
}

?>