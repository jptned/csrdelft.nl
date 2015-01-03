<?php

/**
 * GetalVelden.class.php
 * 
 * @author Jan Pieter Waagmeester <jieter@jpwaag.com>
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * 
 * Bevat de uitbreidingen van TextField:
 * 
 * 	- IntField					Integers 
 * 		* DecimalField				Kommagetallen
 * 			- BedragField			Bedragen met 2 cijfers achter de komma
 * 	- TelefoonField				Telefoonnummers
 */

/**
 * Invoeren van een integer. Eventueel met minima/maxima. Leeg evt. toegestaan.
 */
class IntField extends InputField {

	protected $empty_null = true;
	public $type = 'number';
	public $pattern = '[0-9]+';
	public $step = 1;
	public $min = null;
	public $max = null;
	public $min_alert = null;
	public $max_alert = null;

	public function __construct($name, $value, $description, $min = null, $max = null) {
		if (!is_int($value) AND $value !== null) {
			throw new Exception('Geen int');
		}
		parent::__construct($name, $value, $description, 11);
		if ($min !== null) {
			$this->min = (int) $min;
			$this->min_alert = 'Minimaal ' . $this->min;
		}
		if ($max !== null) {
			$this->max = (int) $max;
			$this->max_alert = 'Maximaal ' . $this->max;
		}
		$this->onkeydown .= <<<JS

	if (event.keyCode == 107 || event.keyCode == 109) {
		event.preventDefault();
		if (event.keyCode == 107) {
			$('#add_{$this->getId()}').click();
		}
		else if (event.keyCode == 109) {
			$('#substract_{$this->getId()}').click();
		}
		return false;
	}
JS;
	}

	public function getValue() {
		if ($this->isPosted()) {
			$this->value = filter_input(INPUT_POST, $this->name, FILTER_SANITIZE_NUMBER_INT);
		}
		if ($this->empty_null AND $this->value === '') {
			$this->value = null;
		} else {
			$this->value = (int) $this->value;
		}
		return $this->value;
	}

	public function validate() {
		if (!parent::validate()) {
			return false;
		}
		// parent checks not null
		if ($this->value == '') {
			return true;
		} elseif (!preg_match('/^' . $this->pattern . '$/', $this->value)) {
			$this->error = 'Alleen gehele getallen toegestaan';
		} elseif ($this->max !== null AND $this->value > $this->max) {
			$this->error = 'Maximale waarde is ' . $this->max . ' ';
		} elseif ($this->leden_mod AND LoginModel::mag('P_LEDEN_MOD')) {
			// exception for leden mod
		} elseif ($this->min !== null AND $this->value < $this->min) {
			$this->error = 'Minimale waarde is ' . $this->min . ' ';
		}
		return $this->error === '';
	}

	public function getHtml($showButtons = true) {
		$html = '';
		if (!$this->readonly AND ! $this->disabled AND ! $this->hidden) {
			if ($this->min !== null AND $this->getValue() === $this->min) {
				$class = 'class="disabled"';
			} else {
				$class = '';
			}
			$style = $showButtons ? 'cursor:pointer;padding:7px;' : 'display:none;';
			$icon = '/plaetjes/knopjes/min.png';
			$html .= <<<HTML
<span id="substract_{$this->getId()}" {$class} style="{$style}"><img src="{$icon}" alt="-" class="icon" width="20" height="20" /></span>
HTML;
		}

		if ($this->min !== null) {
			if ($this->min_alert) {
				$alert = "alert('{$this->min_alert}');";
			} else {
				$alert = '';
			}
			$this->onchange .= <<<JS

	if (parseInt( $(this).val() ) < $(this).attr('min')) {
		{$alert}
		$(this).val( $(this).attr('min') );
	}
	$('#substract_{$this->getId()}').toggleClass('disabled', parseInt( $(this).val() ) <= $(this).attr('min'));
JS;
		}
		if ($this->max !== null) {
			if ($this->max_alert) {
				$alert = "alert('{$this->max_alert}');";
			} else {
				$alert = '';
			}
			$this->onchange .= <<<JS

	if (parseInt( $(this).val() ) >  $(this).attr('max')) {
		{$alert}
		$(this).val( $(this).attr('max') );
	}
	$('#add_{$this->getId()}').toggleClass('disabled', parseInt( $(this).val() ) >=  $(this).attr('max'));
JS;
		}

		$html .= ' <input ' . $this->getInputAttribute(array('type', 'id', 'name', 'class', 'value', 'origvalue', 'pattern', 'disabled', 'readonly', 'maxlength', 'placeholder', 'autocomplete', 'min', 'max', 'step')) . ' /> ';

		if (!$this->readonly AND ! $this->disabled AND ! $this->hidden) {
			if ($this->max !== null AND $this->getValue() === $this->max) {
				$class = 'class="disabled"';
			} else {
				$class = '';
			}
			$icon = '/plaetjes/knopjes/plus.png';
			$html .= <<<HTML
<span id="add_{$this->getId()}" {$class} style="{$style}"><img src="{$icon}" alt="+" class="icon" width="20" height="20" /></span>
HTML;
		}
		return $html;
	}

	public function getJavascript() {
		return parent::getJavascript() . <<<JS

$('#add_{$this->getId()}').click(function () {
	var val = parseInt($('#{$this->getId()}').val());
	if ($(this).hasClass('disabled') || isNaN(val)) {
		return;
	}
	$('#{$this->getId()}').val(val + {$this->step}).change();
});
$('#substract_{$this->getId()}').click(function () {
	var val = parseInt($('#{$this->getId()}').val());
	if ($(this).hasClass('disabled') || isNaN(val)) {
		return;
	}
	$('#{$this->getId()}').val(val - {$this->step}).change();
});
JS;
	}

}

class RequiredIntField extends IntField {

	public $required = true;

}

/**
 * Invoeren van een bedrag in centen, dus precisie van 2 cijfers achter de komma.
 * 
 */
class BedragField extends IntField {

	public $valuta;

	public function __construct($name, $value, $description, $valuta = '€', $min = null, $max = null, $step = 0.01) {
		parent::__construct($name, $value, $description, $min * 100, $max * 100);
		$this->step = $step * 100;
		$this->valuta = $valuta;
	}

	public function getHtml() {
		return $this->valuta . parent::getHtml(false) . '<span class="lichtgrijs">(in centen)</span>';
	}

}

class RequiredBedragField extends BedragField {

	public $required = true;

}

/**
 * TelefoonField
 *
 * is valid als er een enigszins op een telefoonnummer lijkende string wordt
 * ingegeven.
 */
class TelefoonField extends TextField {

	public function validate() {
		if (!parent::validate()) {
			return false;
		}
		// parent checks not null
		if ($this->value == '') {
			return true;
		}
		if (!preg_match('/^([\d\+\-]{10,20})$/', $this->value)) {
			$this->error = 'Geen geldig telefoonnummer.';
		}
		return $this->error === '';
	}

}

class RequiredTelefoonField extends TelefoonField {

	public $required = true;

}

/**
 * Invoeren van een decimaal getal. Eventueel met minima/maxima. Leeg evt. toegestaan.
 */
class DecimalField extends TextField {

	public $precision;
	public $min = null;
	public $max = null;

	public function __construct($name, $value, $description, $precision, $min = null, $max = null, $step = null) {
		parent::__construct($name, $value, $description, $min, $max);
		$this->precision = (int) $precision;
		$this->pattern = '[0-9]*([\.|,][0-9]{' . $this->precision . '})?';
		if ($min !== null) {
			$this->min = (float) $min;
		}
		if ($max !== null) {
			$this->max = (float) $max;
		}
		if (is_float($step)) {
			$this->step = $step;
		} else {
			$this->step = 1.0 / (float) pow(10, $this->precision);
		}
		$this->step = str_replace(',', '.', $this->step); // werkomheen
	}

	public function getValue() {
		if ($this->isPosted()) {
			$this->value = filter_input(INPUT_POST, $this->name, FILTER_SANITIZE_NUMBER_FLOAT);
		}
		if ($this->empty_null AND $this->value === '') {
			$this->value = null;
		} else {
			$this->value = (float) $this->value;
		}
		return $this->value;
	}

	public function validate() {
		if (!parent::validate()) {
			return false;
		}
		// parent checks not null
		if ($this->value == '') {
			return true;
		} elseif (!preg_match('/^' . $this->pattern . '$/', $this->getValue())) {
			$this->error = 'Voer maximaal ' . $this->precision . ' decimalen in';
		} elseif ($this->max !== null AND $this->value > $this->max) {
			$this->error = 'Maximale waarde is ' . $this->max . ' ';
		} elseif ($this->min !== null AND $this->value < $this->min) {
			$this->error = 'Minimale waarde is ' . $this->min . ' ';
		}
		return $this->error === '';
	}

}

class RequiredDecimalField extends DecimalField {

	public $required = true;

}