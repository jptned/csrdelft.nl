<?php

require_once 'model/happie/MenukaartGroepenModel.class.php';

/**
 * MenukaartItem.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Item op menukaart dat bestelt kan worden.
 * 
 */
class HappieMenukaartItem extends PersistentEntity {

	/**
	 * Primary key
	 * @var int
	 */
	public $item_id;
	/**
	 * MenukaartGroep
	 * @var int
	 */
	public $menukaart_groep;
	/**
	 * Naam
	 * @var string
	 */
	public $naam;
	/**
	 * Beschrijving op kaart
	 * @var string
	 */
	public $beschrijving;
	/**
	 * Informatie voor ingredienten allergien
	 * @var string
	 */
	public $allergie_info;
	/**
	 * Prijs in eurocenten
	 * @var int
	 */
	public $prijs;
	/**
	 * Beschikbaar voor x aantal bestellingen
	 * @var int
	 */
	public $aantal_beschikbaar;
	/**
	 * Database table columns
	 * @var array
	 */
	protected static $persistent_attributes = array(
		'item_id'			 => array(T::Integer, false, 'auto_increment'),
		'menukaart_groep'	 => array(T::Integer),
		'naam'				 => array(T::String),
		'beschrijving'		 => array(T::Text),
		'allergie_info'		 => array(T::String),
		'prijs'				 => array(T::Integer),
		'aantal_beschikbaar' => array(T::Integer)
	);
	/**
	 * Database primary key
	 * @var array
	 */
	protected static $primary_key = array('item_id');
	/**
	 * Database table name
	 * @var string
	 */
	protected static $table_name = 'happie_menu';

	public function getPrijsFloat() {
		return (float) $this->prijs / 100.0;
	}

	public function getPrijsFormatted() {
		return '<nobr>€ ' . sprintf('%.2f', $this->getPrijsFloat()) . '</nobr>';
	}

	public function getGroep() {
		return HappieMenukaartGroepenModel::instance()->getGroep($this->menukaart_groep);
	}

}