<?php

/**
 * FormKnoppen.class.php
 * 
 * @author Jan Pieter Waagmeester <jieter@jpwaag.com>
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * 
 * Uitbreidingen van FormulierKnop:
 * 		- SubmitKnop		invoer wordt verzonden
 * 		- ResetKnop			invoer wordt teruggezet naar opgeslagen waarden
 * 		- CancelKnop		invoer wordt genegeerd
 * 
 */
class FormulierKnop implements FormElement {

	protected $id;
	public $url;
	public $action;
	public $icon;
	public $label;
	public $title;
	public $float_left;

	public function __construct($url, $action, $label, $title, $icon, $float_left = false) {
		$this->id = 'knop-' . crc32($url . $action);
		$this->url = $url;
		$this->action = $action;
		$this->label = $label;
		$this->title = $title;
		$this->icon = $icon;
		$this->float_left = $float_left;
	}

	public function getModel() {
		return null;
	}

	public function getTitel() {
		return $this->getType();
	}

	public function getType() {
		return get_class($this);
	}

	public function view() {
		echo '<a id="' . $this->id . '"' . ($this->url ? ' href="' . $this->url . '"' : '') . ' class="knop ' . $this->action . ($this->float_left ? ' float-left' : ' float-right') . '" title="' . $this->title . '">';
		if ($this->icon) {
			echo '<img src="' . CSR_PICS . $this->icon . '" class="icon" width="16" height="16" /> ';
		}
		echo $this->label . '</a> ';
	}

	public function getJavascript() {
		if (strpos($this->action, 'submit') !== false AND isset($this->url)) {
			return "$('#" . $this->id . "').unbind('click.action').bind('click.action', form_set_action);";
		}
		return '';
	}

}

class SubmitKnop extends FormulierKnop {

	public function __construct($url = null, $action = 'submit', $label = 'Opslaan', $title = 'Invoer opslaan', $icon = '/famfamfam/disk.png') {
		parent::__construct($url, $action, $label, $title, $icon);
	}

}

class ResetKnop extends FormulierKnop {

	public function __construct($url = null, $action = 'reset', $label = 'Reset', $title = 'Reset naar opgeslagen gegevens', $icon = '/famfamfam/arrow_rotate_anticlockwise.png') {
		parent::__construct($url, $action, $label, $title, $icon);
	}

}

class CancelKnop extends FormulierKnop {

	public function __construct($url = null, $action = 'cancel', $label = 'Annuleren', $title = 'Niet opslaan en terugkeren', $icon = '/famfamfam/delete.png') {
		parent::__construct($url, $action, $label, $title, $icon);
	}

}

class DeleteKnop extends FormulierKnop {

	public function __construct($url, $action = 'post confirm ReloadPage', $label = 'Verwijderen', $title = 'Definitief verwijderen', $icon = '/famfamfam/cross.png') {
		parent::__construct($url, $action, $label, $title, $icon, true);
	}

}

class FormKnoppen implements FormElement {

	private $knoppen = array();

	public function __construct($cancel_url = null, $icons = true, $label = true, $reset = true, $reset_cancel = false) {
		$this->knoppen['submit'] = new SubmitKnop();
		if ($reset) {
			$this->knoppen['reset'] = new ResetKnop();
		}
		$this->knoppen['cancel'] = new CancelKnop($cancel_url);
		if ($reset_cancel) {
			$this->knoppen['cancel']->action .= ' reset';
			$this->knoppen['submit']->icon = '/famfamfam/accept.png';
		}
		if (!$icons) {
			foreach ($this->knoppen as $knop) {
				$knop->icon = null;
			}
		}
		if (!$label) {
			foreach ($this->knoppen as $knop) {
				$knop->label = null;
			}
		}
	}

	public function getModel() {
		return $this->knoppen;
	}

	public function getTitel() {
		return $this->getType();
	}

	public function getType() {
		return get_class($this);
	}

	public function addKnop(FormulierKnop $knop) {
		$this->knoppen[] = $knop;
	}

	public function view() {
		echo '<div class="' . $this->getType() . '">';
		foreach (array_reverse($this->knoppen) as $knop) {
			$knop->view();
		}
		echo '</div>';
	}

	public function getJavascript() {
		$js = '';
		foreach ($this->knoppen as $knop) {
			$js .= $knop->getJavascript();
		}
		return $js;
	}

}