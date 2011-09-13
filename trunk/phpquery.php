<?php
class phpquery {

private static function return_data ( $data , $type = 'dynamic' ) {

switch ( $type ) {

case 'dynamic':

	if ( $data [ 'successful' ] === false ) {
		return false;
	}else{
		return $data [ 'data' ];
	}

break;
case 'array':

	return array ( 'successful' => $data [ 'successful' ] , 'data' => $data [ 'data' ] , 'error' => $data [ 'error' ] );

break;

}

}

public static function prepare_data ( $data , $return = 'dynamic' ) {

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

public static function run ( $query , $data , $return = 'dynamic' ) {

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

public static function connect ( $dbhost = NULL , $dbuser = NULL , $dbpass = NULL , $dbname = NULL , $return = 'dynamic' ) {

	if ( is_array ( $dbhost ) ) {

		$data = phpquery::prepare_data ( $dbhost );

	}else if ( isset ( $dbhost ) && isset ( $dbuser ) && isset ( $dbpass ) ) {

		$data = phpquery::prepare_data ( array ( $dbhost , $dbuser , $dbpass , $dbname ) );

	}else {

		return phpquery::return_data ( array ( 'successful' => false , 'error' => 'Not acceptable or incomplete data.' ) , $return ) ;

	}

	$assoc [ 0 ] = 'dbhost' ;
	$assoc [ 1 ] = 'dbuser' ;
	$assoc [ 2 ] = 'dbpass' ;
	$assoc [ 3 ] = 'dbname' ;

	$i = 0;

	while ( $assoc [ $i ] ) {
		if ( !isset ( $data [ $i ] ) ) {
			$data [ $i ] = $data [ $assoc [ $i ] ];
		}
		$i++;
	}

	if ( !mysql_connect ( $data [ 0 ] , $data [ 1 ] , $data [ 2 ] ) ) {

		return phpquery::return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		
	}

	if ( isset ( $data [ 3 ] ) ) {

		if ( !mysql_select_db ( $data [ 3 ] ) ) {
			return phpquery::return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		}

	}

	return phpquery::return_data ( array ( 'successful' => true ) , $return );

}

public static function fetch ( $fields , $table , $options = NULL , $res_type = MYSQL_BOTH , $return = 'dynamic' ) {

	if ( is_string ( $fields ) ) {

		$fields = explode( ',' , $fields );

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

public static function disconnect ( $return = 'dynamic' ) {
	$close = mysql_close ();
	$error = null;
	if ( $close == false ) {
		$error = mysql_error ();
	}
	return phpquery::return_data ( array ( 'successful' => $close , 'error' => $error , $return ) );
}

}
?>
