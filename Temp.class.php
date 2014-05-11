<?php

/*
 @nom: Temp
 @auteur: yohanndesbois (ici@nonplus.com)
 @description:  Classe de gestion des capteurs de temperature par pieces
 */

class Temp extends SQLiteEntity{

	protected $id,$name,$description,$capteur,$room,$protocol;
	protected $TABLE_NAME = 'plugin_temp';
	protected $CLASS_NAME = 'Temp';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'capteur'=>'int',
		'protocol'=>'string',
		'room'=>'int'
	);

	function __construct(){
		parent::__construct();
	}

	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}

	function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}

	function getCapteur(){
		return $this->capteur;
	}

	function setCapteur($capteur){
		$this->capteur = $capteur;
	}

	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}
	function getProtocol(){
		return $this->protocol;
	}

	function setProtocol($protocol){
		$this->protocol = $protocol;
	}
}

?>
