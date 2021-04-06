<?php
header( "Access-Control-Allow-Origin: http://localhost:5501" );
header( "Access-Control-Allow-Credentials: true" );
header( "Access-Control-Allow-Methods: GET, DELETE, PUT" );
header( "Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept" );

session_set_cookie_params( ["samesite" => "None", "secure" => "true"] );
session_start( );

$file = "../data/users.json";

if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    $response = new stdClass( );
    $response->errors = [];

    if( !is_file( $file ) ){
        file_put_contents( $file, "[]" );
    }
    $user = json_decode( file_get_contents( "php://input" ) );

    $users = json_decode( file_get_contents( $file ) );
    if( !$users ){
        $users = [];
    }

    // Input validation

    if( empty( $user->email ) ){
        array_push( $response->errors, "Please provide an email address." );

    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push( $response->errors, "Invalid email address." );
    } else {

        for( $i = 0; $i < count( $users ); $i++ ){
            if( $user->email == $users[$i]->email ){
                array_push( $response->errors, "Email address is already in use." );
                break;
            }
        }
    }

    if( empty( $user->firstName ) ){
        array_push( $response->errors, "Please provide a first name." );
    }

    if( empty( $user->lastName ) ){
        array_push( $response->errors, "Please provide a last name." );
    }

    if( !preg_match( "/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $user->password ) ){
        array_push( $response->errors, "Password must contain a number, uppercase letter, lowercase letter, and 8+ characters." );
    }

    // No errors
    if( count( $response->errors ) == 0 ){

        // CRITICAL: Hash password then throw it away
        $user->passwordHash = password_hash( $user->password, PASSWORD_DEFAULT );
        unset( $user->password );

        $user->roles = [];

        array_push( $users, $user );
    
        for( $i = 0; $i < count( $users ); $i++ ){
            $users[$i]->id = $i+1;
        }
    
        file_put_contents( $file, json_encode( $users ) );

        // CRITICAL: Throw away the password hash
        unset( $user->passwordHash );

        $response->user = $user;

        // Login
        $_SESSION["id"] = $user->id;
    }

    echo json_encode( $response );
}

?>