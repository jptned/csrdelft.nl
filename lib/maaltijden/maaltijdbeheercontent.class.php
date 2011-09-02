<?php

# C.S.R. Delft
# -------------------------------------------------------------------
# maaltijden/class.maaltijdbeheercontent.php
# -------------------------------------------------------------------
# Toevoegen en bewerken van maaltijden
# -------------------------------------------------------------------


require_once 'maaltijden/maaltrack.class.php';
require_once 'maaltijden/corveeinstellingen.class.php';


class MaaltijdbeheerContent extends SimpleHTML {

	private $_maaltrack;
	private $_maaltijd=null;

	private $_error='';

	function __construct($maaltrack) {
		$this->_maaltrack=$maaltrack;
	}
	function getTitel(){ return 'Maaltijdketzer - beheer'; }

	//functie om een maaltijd in het formulier te laden, normaal gewoon een formulier voor nieuwe maaltijden.
	function load($iMaalID){
		$iMaalID=(int)$iMaalID;
		$this->_maaltijd=$this->_maaltrack->getMaaltijd($iMaalID);
	}
	function addError($error){ $this->_error=$error; }

	function view(){
		$loginlid=LoginLid::instance();
		//de html template in elkaar draaien en weergeven
		$maaltijdbeheer=new Smarty_csr();
		$maaltijdbeheer->caching=false;

		//Dingen ophalen voor het overzicht van maaltijden...
		$aMaal['error']=$this->_maaltrack->getError();
		$begin = strtotime(Corveeinstellingen::get('periodebegin'));
		$eind = strtotime(Corveeinstellingen::get('periodeeind'));
		$aMaal['maaltijden']=$this->_maaltrack->getMaaltijden($begin, $eind, false);


		//nieuwe maaltijd, of oude bewerken?
		if($this->_maaltijd==null OR !is_array($this->_maaltijd)){
			//nieuwe maaltijd, standaardwaarden
			$aForm['id']=0;
			$aForm['actie']='toevoegen';
			$aForm['datum']=time();
			$aForm['abosoort']='';
			$aForm['max']=100;
			//alles standaard naar jan lid.
			$aForm['tp']='x101';

			$aForm['koks']=2;
			$aForm['afwassers']=3;
			$aForm['theedoeken']=1;
			
			//corveepunten voor een standaardmaaltijd
		    $aForm['punten_kok']=Corveeinstellingen::get('puntenkoken');
			$aForm['punten_afwas']=Corveeinstellingen::get('puntenafwas');
			$aForm['punten_theedoek']=Corveeinstellingen::get('puntentheedoek');
		}else{
			$aForm=$this->_maaltijd;
			$aForm['actie']='bewerken';
		}
		# als er een error gegeven wordt, is er hoogstwaarschijnlijk wat mis gegaan bij het bewerken of toevoegen
		# van een nieuwe maaltijd. Daarom kijken we hier of er nog zinnige invoer uit de post te halen valt.
		if($this->_error!=''){
			if(isset($_POST['datum'])){ $aForm['datum']=trim(mb_htmlentities($_POST['datum'])); }
			if(isset($_POST['tekst'])){ $aForm['tekst']=trim(mb_htmlentities($_POST['tekst'])); }
			if(isset($_POST['limiet']) AND $_POST['limiet']==(int)$_POST['limiet']){ $aForm['max']=$_POST['limiet']; }
			if(isset($_POST['abo']) AND $this->_maaltrack->isValidAbo($_POST['abo'])){ $aForm['abosoort']=$_POST['abo']; }
			if(isset($_POST['tp']) AND Lid::exists($_POST['tp']) ){ $aForm['tp']=$_POST['tp']; }
			if(isset($_POST['koks'])){  $aForm['koks']=(int)$_POST['koks']; }
			if(isset($_POST['afwassers'])){  $aForm['afwassers']=(int)$_POST['afwassers']; }
			if(isset($_POST['theedoeken'])){  $aForm['theedoeken']=(int)$_POST['theedoeken']; }
		}
		$aForm['abos']=$this->_maaltrack->getAbos();
		$aMaal['formulier']=$aForm;

		//arrays toewijzen en weergeven
		$maaltijdbeheer->assign('maal', $aMaal);
		$maaltijdbeheer->assign('toonLijsten', $loginlid->hasPermission('P_MAAL_MOD') or opConfide());
		$maaltijdbeheer->assign('datumFormaat', '%a %e %b %H:%M');
		$maaltijdbeheer->assign('datumFormaatInvoer', '%Y-%m-%d 18:00');
		if($this->_error!=''){ $maaltijdbeheer->assign('error', $this->_error); }
		$maaltijdbeheer->display('maaltijdketzer/beheer.tpl');
	}
}

?>
