<?php
/**
 * FileNameField.class.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 */
class FileNameField extends TextField {

	public function validate() {
		if (!parent::validate()) {
			return false;
		}
		if ($this->value !== '' AND !valid_filename($this->value)) {
			$this->error = 'Ongeldige bestandsnaam';
		}
		return $this->error === '';
	}

}
