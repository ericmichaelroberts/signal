<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Signal extends DMSA {

	private static $TableChecked = false;

	private static function InitTable(){

		$ci =& get_instance();

		return self::$TableChecked = !$ci->db->table_exists( 'signals' )
			?	$this->db->query(implode(' ',[
					"CREATE TABLE signals (",
						"signal_id INT(11) unsigned NOT NULL AUTO_INCREMENT,",
						"origin VARCHAR(64) NOT NULL,",
						"identifier INT(11) NOT NULL DEFAULT '0',",
						"data TEXT,",
						"created_at DATETIME NOT NULL,",
						"PRIMARY KEY ( signal_id ),",
						"KEY origin_identifier ( origin, identifier )",
					") ENGINE=InnoDB DEFAULT CHARSET=utf8"
				]))
			:	true;
	}

	protected $origin;
	protected $identifier =0;

	public function __construct( $origin ){
		$this->origin = $origin;
		if( !self::$TableChecked ){
			self::InitTable();
		}
	}

	public function __invoke( $data=null ){
		return $this->store( $data );
	}

	public function store( $data=null ){
		$ci =& get_instance();
		return $ci->db->query($ci->db->insert_string(
			'signals',
			[
				'origin'		=>	$this->origin,
				'identifier'	=>	$this->identifier++,
				'data'			=>	is_scalar( $data )
					?	( is_bool( $data )
						?	( !!$data
							?	'TRUE'
							:	'FALSE')
						:	$data )
					:	json_encode(
							$data,
							JSON_NUMERIC_CHECK ),
				'created_at'	=> date('Y-m-d H:i:s')
			]
		));
	}

	


}
