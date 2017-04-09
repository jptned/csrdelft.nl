<?php
require_once 'model/entity/fiscaat/CiviSaldo.class.php';

/**
 * BeheerCiviSaldoView.class.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/04/2017
 */
class BeheerCiviSaldoView extends DataTable {
	public function __construct() {
		parent::__construct(CiviSaldo::class, '/fiscaat/saldo', 'Saldobeheer');

		$this->addColumn('naam', 'saldo');
		$this->addColumn('lichting', 'saldo');
		$this->addColumn('saldo', null, null, null, null, 'num');
		$this->hideColumn('saldo');
		$this->addColumn('saldo_', 'laatst_veranderd', null, 'prijs_render', 'saldo');
		$this->setOrder(array('saldo_' => 'asc'));

		$this->searchColumn('naam');

		$this->addKnop(new DataTableKnop('== 0', $this->dataTableId, '/fiscaat/saldo/registreren', 'post', 'Registreren', 'Lid registreren', 'toevoegen'));
	}

	public function getBreadcrumbs() {
		return '<a href="/fiscaat"><span class="fa fa-eur module-icon"></span></a> » Saldo';
	}

	public function getJavascript() {
		return /** @lang JavaScript */
			parent::getJavascript() . <<<JS
function prijs_render(data, type, row) {
	return "&euro; " + (row.saldo/100).toFixed(2).replace('.', ',');
}
JS;
	}
}

/**
 * Class LidRegistratieForm
 *
 * Maak het mogelijk om een lid te registreren, wordt uiteindelijk samengetrokken met het aanmaken van een lid.
 */
class LidRegistratieForm extends ModalForm {
	public function __construct(CiviSaldo $model) {
		parent::__construct($model, '/fiscaat/saldo/registreren/lid', false, true);

		$fields[] = new LidField('uid', $model->uid, 'Lid');
		$fields[] = new IntField('saldo', $model->saldo, 'Initieel saldo');
		$fields['btn'] = new FormDefaultKnoppen();

		$this->addFields($fields);
	}
}

class BeheerSaldoResponse extends DataTableResponse {
	/**
	 * @param CiviSaldo $entity
	 * @return string
	 */
	public function getJson($entity) {
		return parent::getJson(array(
			'uid' => $entity->uid,
			'naam' => ProfielModel::getNaam($entity->uid, 'volledig'),
			'lichting' => substr($entity->uid, 0, 2),
			'saldo' => $entity->saldo,
			'laatst_veranderd' => $entity->laatst_veranderd
		));
	}
}
