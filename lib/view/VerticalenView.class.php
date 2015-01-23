<?php
require_once 'model/entity/LidStatus.enum.php';

/**
 * VerticalenView.class.php
 * 
 * @author C.S.R. Delft <pubcie@csrdelft.nl>
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Weergeven van verticalen.
 */
class VerticalenView implements View {

	private $verticalen;

	public function __construct($verticalen) {
		$this->verticalen = $verticalen;
	}

	public function getModel() {
		return $this->verticalen;
	}

	public function getBreadcrumbs() {
		return '<a href="/ledenlijst" title="Ledenlijst"><span class="fa fa-user module-icon"></span></a> » <span class="active">' . $this->getTitel() . '</span>';
	}

	public function getTitel() {
		return 'Verticalen der Civitas';
	}

	public function view() {
		?><ul class="horizontal nobullets">
			<li>
				<a href="/ledenlijst">Ledenlijst</a>
			</li>
			<li>
				<a href="/leden/verjaardagen" title="Overzicht verjaardagen">Verjaardagen</a>
			</li>
			<li class="active">
				<a href="/verticalen">Kringen</a>
			</li>
		</ul>
		<hr />
		<?php
		foreach ($this->verticalen as $verticale) {
			if ($verticale->letter == '') {
				continue;
			}
			echo '<div class="verticale"><a name="' . $verticale->letter . '">&nbsp;</a>';
			echo '<h2>Verticale ' . $verticale->naam . '</h2>';

			foreach ($verticale->getKringen() as $kringnaam => $kring) {
				$kringstyle = 'kring';
				if ($kringnaam == 0) {
					$kringstyle = 'geenkring';
				}
				echo '<div class="' . $kringstyle . '" id="kring' . $verticale->letter . '.' . $kringnaam . '">';
				echo '<div class="mailknopje" onclick="toggleEmails(\'' . $verticale->letter . '.' . $kringnaam . '\')">@</div>';
				if ($kringnaam == 0) {
					echo '<h5>Geen kring</h5>';
				} else {
					echo '<h5>Kring ' . $kringnaam . '</h5>';
				}
				echo '<div id="leden' . $verticale->letter . '.' . $kringnaam . '" class="kringleden">';
				foreach ($kring as $profiel) {
					if ($profiel->kringleider !== Kringleider::Nee) {
						echo '<span class="cursief">';
					}
					echo $profiel->getLink('volledig');
					if ($profiel->status === LidStatus::Kringel) {
						echo ' ~';
					}
					if ($profiel->verticaleleider) {
						echo ' L';
					}
					if ($profiel->kringcoach) {
						echo ' <span title="Kringcoach van verticale ' . VerticalenModel::get($profiel->kringcoach)->naam . '">C</span>';
					}
					if ($profiel->kringleider !== Kringleider::Nee) {
						echo '</span>';
					}
					echo '<br />';
				}
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		}
		// bottom spacing
		for ($i = 0; $i < 11; $i++) {
			echo '<p>&nbsp;</p>';
		}
		?>
		<script type="text/javascript">
			if (document.location.hash.substring(1, 6) == 'kring') {
				kring = document.location.hash.substring(1);
				document.getElementById(kring).style.backgroundColor = '#f5f5f5';
				document.getElementById(kring).style.borderBottom = '1px solid black';
			}
		</script>
		<?php
	}

}

class VerticaleEmailsView implements View {

	private $verticale;
	private $kring;

	public function __construct($vertkring) {
		try {
			$this->verticale = VerticalenModel::get(substr($vertkring, 0, 1));
			$this->kring = $this->verticale->getKring((int) substr($vertkring, 2, 1));
		} catch (Exception $e) {
			setMelding($e->getMessage(), -1);
			$this->kring = array();
		}
	}

	public function view() {
		$leden = array();
		foreach ($this->kring as $kringlid) {
			$leden[] = $kringlid->getPrimaryEmail();
		}
		echo implode(', ', $leden);
	}

	public function getBreadcrumbs() {
		return null;
	}

	public function getModel() {
		return $this->kring;
	}

	public function getTitel() {
		return null;
	}

}
