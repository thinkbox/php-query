<?php
class phpquery {

private static function return_data ( $data , $type = 'dynamic' ) {

$return = array ( 'successful' => $data [ 'successful' ] , 'data' => $data [ 'data' ] , 'error' => $data [ 'error' ] );

switch ( $type ) {

case 'dynamic':

	if ( $return [ 'successful' ] === false ) {
		return false;
	}else{
		return $return [ 'data' ];
	}

break;
case 'array':

	return $return;

break;
case 'json':
	return json_encode ( $return );
break;

}

}

public static function prepare_data ( $data = '' , $return = 'dynamic' ) {

	if ( is_string ( $data ) ) {
	
		$data = explode ( ',' , $data );

	}else if ( !is_array ( $data ) ) {
	
		return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Inacceptible Data Type' ) , $return  ) ;

	}

	
	$i=0;
	while ( $data[ $i ] ) {
		$data[ $i ] = addslashes( $data [ $i ] );
		$i++;
	}
	
	return phpquery::return_data ( array ( 'successful' => true , 'data' => $data ) , $return );

}

public static function run ( $query = '' , $data = '' , $return = 'dynamic' ) {

	if ( isset ( $data ) ) {

		if ( is_string ( $data ) ) {
		
			$data = explode ( ',' , $data );
			
		}else if ( !is_array ( $data ) ) {

			return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Not acceptable data type.' ) , $return );
		
		}

		$data = phpquery::prepare_data ( $data , 'array' );

		if ( $data [ 'successful' ] === false ) {
			return phpquery::return_data ( $data , $return );
		}

		$data = $data [ 'data' ];

		$parts = explode( '?' , $query );

		$query = '';

		foreach ( $data as $value ) {

			$query .= array_shift( $parts );

			$query .= $value;

		}
	
		$query .= array_shift( $parts );

	}

	$res = mysql_query ( $query );

	if ( is_resource ( $res ) ) {

		$r [ 'successful' ] = true;
		$r [ 'data' ] = $res;

	}else {
		$r [ 'successful' ] = $res;

		if ( $res === false ) {
			$r [ 'error' ] = mysql_error ();
		}

	}

	return phpquery::return_data ( $r , $return );
}

public static function connect ( $dbhost = 'localhost' , $dbuser = 'root' , $dbpass = '' , $peristent = false , $dbname = NULL , $return = 'dynamic' ) {

	if ( is_array ( $dbhost ) ) {

		$data = phpquery::prepare_data ( $dbhost );

	}else if ( isset ( $dbhost ) && isset ( $dbuser ) && isset ( $dbpass ) ) {

		$data = phpquery::prepare_data ( array ( $dbhost , $dbuser , $dbpass , $persistent , $dbname ) );

	}else {

		return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Not acceptable or incomplete data.' ) , $return ) ;

	}

	$assoc [ 0 ] = 'dbhost' ;
	$assoc [ 1 ] = 'dbuser' ;
	$assoc [ 2 ] = 'dbpass' ;
	$assoc [ 3 ] = 'persistent' ;
	$assoc [ 4 ] = 'dbname' ;

	$i = 0;

	while ( $assoc [ $i ] ) {
		if ( !isset ( $data [ $i ] ) ) {
			$data [ $i ] = $data [ $assoc [ $i ] ];
		}
		$i++;
	}

	if ( ( $data [ 3 ] === true ) || ( $data [ 3 ] === 'persistent' ) ) {

		$link = mysql_pconnect ( $data [ 0 ] , $data [ 1 ] , $data [ 2 ] ) ;
	
	}else{
		$link = mysql_connect ( $data [ 0 ] , $data [ 1 ] , $data [ 2 ] ) ;
	
	}


	if ( !$link ) {

		return phpquery::return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		
	}

	if ( isset ( $data [ 4 ] ) ) {

		if ( !mysql_select_db ( $data [ 4 ] ) ) {
			return phpquery::return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		}

	}

	return phpquery::return_data ( array ( 'successful' => true ) , $return );

}

public static function fetch ( $fields = '' , $table = '' , $options = NULL , $res_type = MYSQL_BOTH , $return = 'dynamic' ) {

	if ( is_string ( $fields ) ) {

		if ( strpos ( $fields , ',' ) != false ) {

			$fields = array ( $fields );
		
		}else{

			$fields = explode( ',' , $fields );

		}

	}else if ( !is_array ( $fields ) ) {
	
		return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Not acceptable data type for \'fields\'.' ) );

	}

	if ( is_string ( $table ) ) {

		$table = array ( $table );
		
	}else if ( !is_array ( $table ) ) {

		return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Not acceptable data type for \'table\'.' ) );

	}

	$fields = phpquery::prepare_data ( $fields , $return );
	
	$fields = implode ( ' , ' , $fields [ 'data' ] );

	$table = implode ( '' , $table );

	$query = "SELECT $fields FROM $table $options;";

	$res = mysql_query ( $query );

	if ( !is_resource ( $res ) ) {
		return phpquery::return_data ( array ( 'successful' => false , 'data' => $query , 'error' => mysql_error () ) , $return );
	}

	$rows = array();
	while ( $row = mysql_fetch_array( $res , $res_type ) ) {
		$rows[] = $row;
	}

	return phpquery::return_data ( array ( 'successful' => true , 'data' => $rows ) );
	
}

public static function disconnect ( $link = null , $return = 'dynamic' ) {

	if ( isset ( $link ) ) {
		$close = mysql_close ( $link );
	}else{
		$close = mysql_close ();
	}

	$error = null;
	if ( $close == false ) {
		$error = mysql_error ();
	}
	return phpquery::return_data ( array ( 'successful' => $close , 'error' => $error ) , $return );
}

}
?>
