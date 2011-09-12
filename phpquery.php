<?php
class phpquery {

private function return_data ( $data , $type = 'dynamic' ) {

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

public function prepare_mysql_data ( $data , $return = 'dynamic' ) {

	if ( is_string ( $data ) ) {
	
		$data = explode ( ',' , $data );

	}else if ( !is_array ( $data ) ) {
	
		return $this -> return_data ( array ( 'successful' => false , 'error' => 'Inacceptible Data Type' ) , $return  ) ;

	}

	
	$i=0;
	while ( $data[ $i ] ) {
		$data[ $i ] = addslashes( $data [ $i ] );
		$i++;
	}
	
	return $this -> return_data ( array ( 'successful' => true , 'data' => $data ) , $return );

}

public function run ( $query , $data , $return = 'dynamic' ) {

	$data = $this -> prepare_mysql_data ( $data , 'array' );

	if ( $data [ 'successful' ] === false ) {
		return $this -> return_data ( $data , $return );
	}

	$parts = explode( '?' , $query );

	$query = '';

	foreach ( $data as $value ) {

		$query .= array_shift( $parts );

		$query .= $value;

	}
	
	$query .= array_shift( $parts );

	$res = mysql_query ( $query );

	$r = array ();

	if ( is_resource ( $res ) ) {

		$r [ 'successful' ] = true;
		$r [ 'data' ] = $res;

	}else {

		$r [ 'successful' ] = $res;

		if ( $res === false ) {
			$r [ 'error' ] = mysql_error ();
		}

	}

	return $this -> return_data ( $r , $return );
}

public function connect ( $dbhost = NULL , $dbuser = NULL , $dbpass = NULL , $dbname = NULL , $return = 'dynamic' ) {

	if ( is_array ( $dbhost ) ) {

		$data = $this -> prepare_mysql_data ( $dbhost );

	}else if ( isset ( $dbhost ) && isset ( $dbuser ) && isset ( $dbpass ) ) {

		$data = $this -> prepare_mysql_data ( array ( $dbhost , $dbuser , $dbpass , $dbname ) );

	}else {

		return $this -> return_data ( array ( 'successful' => false , 'error' => 'Not acceptable or incomplete data.' ) , $return ) ;

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

		return $this -> return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		
	}

	if ( isset ( $data [ 3 ] ) ) {

		if ( !mysql_select_db ( $data [ 3 ] ) ) {
			return $this -> return_data ( array ( 'successful' => false , 'error' => mysql_error () ) , $return );
		}

	}

	return $this -> return_data ( array ( 'successful' => true ) , $return );

}

}
?>
