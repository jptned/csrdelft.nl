<?php
/**
 * GesprekForm.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/05/2017
 */

namespace CsrDelft\view\gesprekken;

use CsrDelft\model\security\LoginModel;
use CsrDelft\view\formulier\invoervelden\required\RequiredLidField;
use CsrDelft\view\formulier\invoervelden\required\RequiredTextareaField;
use CsrDelft\view\formulier\knoppen\FormDefaultKnoppen;
use CsrDelft\view\formulier\ModalForm;

class GesprekForm extends ModalForm {

	public function __construct() {
		parent::__construct(null, '/gesprekken/start', 'Nieuw gesprek');
		$this->css_classes[] = 'redirect';

		$fields = [];
		$fields['to'] = new RequiredLidField('to', null, 'Naam of lidnummer');
		$fields['to']->blacklist = array(LoginModel::getUid());
		$fields[] = new RequiredTextareaField('inhoud', null, 'Bericht');

		$this->addFields($fields);

		$this->formKnoppen = new FormDefaultKnoppen(null, false);
	}

}
