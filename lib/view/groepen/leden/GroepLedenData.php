<?php

namespace CsrDelft\view\groepen\leden;

use CsrDelft\model\ProfielModel;
use CsrDelft\view\datatable\DataTableResponse;

class GroepLedenData extends DataTableResponse {

	public function getJson($lid) {
		$array = $lid->jsonSerialize();

		$array['uid'] = ProfielModel::getLink($array['uid'], 'civitas');
		$array['door_uid'] = ProfielModel::getLink($array['door_uid'], 'civitas');

		return parent::getJson($array);
	}

}
