<?php

namespace CsrDelft\view\peilingen;

use CsrDelft\model\entity\peilingen\PeilingOptie;
use CsrDelft\model\security\LoginModel;
use CsrDelft\view\formulier\getalvelden\IntField;
use CsrDelft\view\formulier\invoervelden\HiddenField;
use CsrDelft\view\formulier\invoervelden\LidField;
use CsrDelft\view\formulier\invoervelden\RequiredTextField;
use CsrDelft\view\formulier\invoervelden\TextareaField;
use CsrDelft\view\formulier\knoppen\FormDefaultKnoppen;
use CsrDelft\view\formulier\ModalForm;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 30/10/2018
 */
class PeilingOptieForm extends ModalForm
{
	/**
	 * @param PeilingOptie $model
	 * @param $id
	 * @throws \CsrDelft\common\CsrGebruikerException
	 */
	public function __construct($model, $id)
	{
		parent::__construct($model,'/peilingen/opties/' . $id . '/toevoegen', 'Optie toevoegen', true);

		$fields = [];
		$fields[] = new HiddenField('peiling_id', $model->peiling_id);
		$fields[] = new RequiredTextField('titel', $model->titel, 'Titel');
		$fields[] = new TextareaField('beschrijving', $model->beschrijving, 'Beschrijving');

		$this->addFields($fields);

		$this->formKnoppen = new FormDefaultKnoppen();
	}
}
