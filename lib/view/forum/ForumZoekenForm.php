<?php

namespace CsrDelft\view\forum;

use CsrDelft\view\formulier\elementen\HtmlComment;
use CsrDelft\view\formulier\Formulier;
use CsrDelft\view\formulier\getalvelden\IntField;
use CsrDelft\view\formulier\invoervelden\TextField;
use CsrDelft\view\formulier\keuzevelden\SelectField;

class ForumZoekenForm extends Formulier {

	public function __construct() {
		parent::__construct(null, '/forum/zoeken');
		$this->showMelding = false;
		$this->formId = 'forumZoekenForm';
		$this->css_classes[] = 'hoverIntent';

		$fields = [];
		$fields[] = new HtmlComment('<div class="forumZoekenGeavanceerd hoverIntentContent verborgen"><div class="form-inline">');
		$fields[] = new SelectField('datumsoort', 'laatst_gewijzigd', null, array('laatst_gewijzigd' => 'Laatste reactie', 'datum_tijd' => 'Aanmaak-datum'));
		$fields[] = new SelectField('ouderjonger', 'jonger', null, array('jonger' => 'Niet', 'ouder' => 'Wel'));
		$fields[] = new HtmlComment('<div class="mx-1"> ouder dan </div>');
		$fields[] = new IntField('jaaroud', 1, null, 0, 99);
		$fields[] = new HtmlComment('<div class="mx-l"> jaar</div></div></div>'); /*
		  $fields['l'] = new LidField('auteur', null, 'Auteur');
		  $fields['l']->no_preview = true; */

		$fields['z'] = new TextField('zoekopdracht', null, null);
		$fields['z']->placeholder = 'Zoeken in forum';
		$fields['z']->enter_submit = true;

		$this->addFields($fields);
	}

}
