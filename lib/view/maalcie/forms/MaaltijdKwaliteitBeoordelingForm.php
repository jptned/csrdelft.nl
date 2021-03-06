<?php

namespace CsrDelft\view\maalcie\forms;

use CsrDelft\model\entity\maalcie\Maaltijd;
use CsrDelft\model\entity\maalcie\MaaltijdBeoordeling;
use CsrDelft\model\InstellingenModel;
use CsrDelft\view\formulier\InlineForm;
use CsrDelft\view\formulier\keuzevelden\SterrenField;


class MaaltijdKwaliteitBeoordelingForm extends InlineForm {

	public function __construct(Maaltijd $maaltijd, MaaltijdBeoordeling $beoordeling) {

		$field = new SterrenField('kwaliteit', $beoordeling->kwaliteit, null, 4);
		$field->hints = array('ruim onvoldoende', 'onvoldoende', 'voldoende', 'ruim voldoende');
		$field->click_submit = true;
		$field->readonly = $maaltijd->getBeginMoment() < strtotime(InstellingenModel::get('maaltijden', 'beoordeling_periode'));

		parent::__construct($beoordeling, maalcieUrl . '/beoordeling/' . $beoordeling->maaltijd_id, $field, false);
		$this->css_classes[] = 'noanim';
	}

}
