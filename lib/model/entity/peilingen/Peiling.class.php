<?php

namespace CsrDelft\model\entity\peilingen;

use CsrDelft\model\peilingen\PeilingOptiesModel;
use CsrDelft\model\peilingen\PeilingStemmenModel;
use CsrDelft\model\security\LoginModel;
use CsrDelft\Orm\Entity\PersistentEntity;
use CsrDelft\Orm\Entity\T;

/**
 * Class Peiling
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 */
class Peiling extends PersistentEntity {
	public $id;
	public $titel;
	public $beschrijving;
	public $eigenaar;
	public $mag_bewerken;
	public $resultaat_zichtbaar;
	public $aantal_voorstellen;
	public $aantal_stemmen;
	public $rechten_stemmen;

	private $opties;

	public function getStemmenAantal() {
		return PeilingStemmenModel::instance()->count('peiling_id = ?', array($this->id));
	}

	public function getOpties() {
		if ($this->opties == null) {
			$this->opties = PeilingOptiesModel::instance()->find('peiling_id = ?', array($this->id))->fetchAll();
		}
		return $this->opties;
	}

	public function nieuwOptie($optie) {
		$this->opties[] = $optie;
	}

	public static function magBewerken() {
		//Elk BASFCie-lid heeft voorlopig peilingbeheerrechten.
		return LoginModel::mag('P_ADMIN,bestuur,commissie:BASFCie');
	}

	public function isMod() {
		return LoginModel::mag('P_PEILING_MOD') || LoginModel::getUid() == $this->eigenaar;
	}

	public function magStemmen() {
		if (!LoginModel::mag('P_LOGGED_IN')) {
			return false;
		}
		return !$this->heeftGestemd(LoginModel::getUid());
	}

	public function heeftGestemd($uid) {
		return PeilingStemmenModel::instance()->heeftGestemd($this->id, $uid);
	}

	protected static $table_name = 'peiling';
	protected static $primary_key = array('id');
	protected static $persistent_attributes = array(
		'id' => [T::Integer, false, 'auto_increment'],
		'titel' => [T::String],
		'beschrijving' => [T::Text],
		'eigenaar' => [T::UID],
		'mag_bewerken' => [T::Boolean],
		'resultaat_zichtbaar' => [T::Boolean],
		'aantal_voorstellen' => [T::Integer],
		'aantal_stemmen' => [T::Integer],
		'rechten_stemmen' => [T::String, true],
	);
}


