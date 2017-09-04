<?php

namespace CsrDelft\model\entity\fiscaat;

use CsrDelft\Orm\Entity\PersistentEnum;

/**
 * CiviSaldoCommissieEnum.class.php
 *
 * Maak onderscheid tussen verschillende commissies die uit hetzelfde potje geld halen.
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/04/2017
 */
class CiviSaldoCommissieEnum extends PersistentEnum {

	const MAALCIE = 'maalcie';
	const SOCCIE = 'soccie';
	const OWEECIE = 'oweecie';
	const ANDERS = 'anders';

	public static function getTypeOptions() {
		return array(self::ANDERS, self::SOCCIE, self::MAALCIE, self::OWEECIE);
	}

	public static function getDescription($option) {
		return sprintf('Commissie: %s', $option);
	}

	public static function getChar($option) {
		return ucfirst($option);
	}
}
