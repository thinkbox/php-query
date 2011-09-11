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

}
?>
