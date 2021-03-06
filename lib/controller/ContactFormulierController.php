<?php

namespace CsrDelft\controller;
use CsrDelft\common\CsrToegangException;
use CsrDelft\common\Ini;
use CsrDelft\common\SimpleSpamFilter;
use CsrDelft\controller\framework\AclController;
use CsrDelft\model\entity\Mail;
use CsrDelft\view\JsonResponse;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 19/12/2018
 */
class ContactFormulierController extends AclController {
	public function __construct($query) {
		parent::__construct($query, null, ['GET', 'POST']);

		if ($this->getMethod() == 'POST') {
			$this->acl = [
				'dies' => 'P_PUBLIC',
				'interesse' => 'P_PUBLIC',
			];
		} else {
			$this->acl = [];
		}
	}

	public function performAction(array $args = array()) {
		if ($this->hasParam(2)) {
			$this->action = $this->getParam(2);
		}
		return parent::performAction();
	}

	public function POST_dies() {
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$naam = filter_input(INPUT_POST, 'naam', FILTER_SANITIZE_STRING);
		$verhaal = filter_input(INPUT_POST, 'verhaal', FILTER_SANITIZE_STRING);

		if ($this->isSpam($email, $naam, $verhaal)) {
			throw new CsrToegangException("spam", 400);
		}

		if ($email && $naam && $verhaal) {
			$mail = new Mail([Ini::lees(Ini::EMAILS, 'diescie') => 'DiesCie'], 'Bericht van de stek', <<<TEXT
Beste DiesCie,

Er is een bericht geplaatst via de dies webstek.

Email: $email
Over mij: 
$naam

Over mij date:
$verhaal

Groetjes,
Feut
namens de PubCie
TEXT
			);
			$mail->setFrom($email);
			$mail->send();

			$this->view = new JsonResponse(true);
		} else {
			throw new CsrToegangException("Verzenden mislukt", 400);
		}
	}

	public function POST_interesse() {
		$naam = filter_input(INPUT_POST, "naam", FILTER_SANITIZE_STRING);
		$email = filter_input(INPUT_POST, "submit_by", FILTER_SANITIZE_STRING);
		$adres = filter_input(INPUT_POST, "straat", FILTER_SANITIZE_STRING);
		$postcode = filter_input(INPUT_POST, "postcode", FILTER_SANITIZE_STRING);
		$woonplaats = filter_input(INPUT_POST, "plaats", FILTER_SANITIZE_STRING);
		$telefoon = filter_input(INPUT_POST, "telefoon", FILTER_SANITIZE_STRING);
		$opmerking = filter_input(INPUT_POST, "opmerking", FILTER_SANITIZE_STRING);

		$interesses = [];

		$interesse1 = filter_input(INPUT_POST, "interesse1", FILTER_SANITIZE_STRING);
		$interesse2 = filter_input(INPUT_POST, "interesse2", FILTER_SANITIZE_STRING);
		$interesse3 = filter_input(INPUT_POST, "interesse3", FILTER_SANITIZE_STRING);
		$interesse4 = filter_input(INPUT_POST, "interesse4", FILTER_SANITIZE_STRING);

		if ($interesse1) array_push($interesses, $interesse1);
		if ($interesse2) array_push($interesses, $interesse2);
		if ($interesse3) array_push($interesses, $interesse3);
		if ($interesse4) array_push($interesses, $interesse4);

		$interessestring = '';
		foreach ($interesses as $interesse) $interessestring .= " * " . $interesse . "\n";

		if ($this->isSpam($naam, $email, $adres, $postcode, $woonplaats, $telefoon, $opmerking, $interessestring)) {
			throw new CsrToegangException("spam", 400);
		}

		$bericht = "
Beste OweeCie,

Het interesseformulier op de stek is ingevuld:

Naam: $naam
Email: $email
Adres: $adres
Postcode: $postcode
Woonplaats: $woonplaats
Telefoon: $telefoon

Interesses:
$interessestring
Opmerking:
$opmerking


Met vriendelijke groeten,
De PubCie.
";

		$mail = new Mail([Ini::lees(Ini::EMAILS, 'oweecie') => "OweeCie", $email => $naam], "Interesseformulier", $bericht);
		$mail->setFrom($email);
		$mail->send();

		setMelding('Bericht verzonden.', 1);
		redirect('/#contact-form');
	}

	private function isSpam(string... $input) {
		$filter = new SimpleSpamFilter();
		foreach ($input as $item) {
			if ($filter->isSpam($item)) {
				return true;
			}
		}
		return false;
	}
}
