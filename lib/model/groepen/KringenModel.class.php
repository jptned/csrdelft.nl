<?php

class KringenModel extends AbstractGroepenModel {

	const ORM = Kring::class;

	protected static $instance;
	/**
	 * Default ORDER BY
	 * @var string
	 */
	protected $default_order = 'verticale ASC, kring_nummer ASC';

	public static function get($id) {
		$kringen = static::instance()->prefetch('verticale = ? AND kring_nummer = ?', explode('.', $id), null, null, 1);
		return reset($kringen);
	}

	public function nieuw($letter = '') {
		$kring = parent::nieuw();
		$kring->verticale = $letter;
		return $kring;
	}

	public function getKringenVoorVerticale(Verticale $verticale) {
		return $this->prefetch('verticale = ?', array($verticale->letter));
	}

}