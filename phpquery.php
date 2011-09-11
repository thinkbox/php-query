<?php
class phpquery {

private function return_data ( $data , $type ) {

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
	
	return $this -> return_data ( array ( 'successful' => true , 'data' => $data ) );

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

	return mysql_query( $query ) or die( mysql_error() );
}

}
?>
