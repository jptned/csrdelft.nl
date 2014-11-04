<?php

/**
 * AccessControl.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * ACL-entry.
 * 
 */
class AccessControl extends PersistentEntity {

	/**
	 * AclController
	 * @var string
	 */
	public $environment;
	/**
	 * Action
	 * @var string
	 */
	public $action;
	/**
	 * UUID
	 * @var string
	 */
	public $resource;
	/**
	 * Rechten
	 * @var string
	 */
	public $subject;
	/**
	 * Database table columns
	 * @var array
	 */
	protected static $persistent_attributes = array(
		'environment'	 => array(T::String),
		'action'		 => array(T::String),
		'resource'		 => array(T::String),
		'subject'		 => array(T::String)
	);
	/**
	 * Database primary key
	 * @var array
	 */
	protected static $primary_key = array('environment', 'action', 'resource');
	/**
	 * Database table name
	 * @var string
	 */
	protected static $table_name = 'acl';

}