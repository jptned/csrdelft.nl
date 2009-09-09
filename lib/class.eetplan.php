<?php
# C.S.R. Delft | pubcie@csrdelft.nl
# -------------------------------------------------------------------
# class.eetplan.php
# -------------------------------------------------------------------
# Verzorgt het opvragen van eetplangegevens
# -------------------------------------------------------------------


require_once ('class.mysql.php');

class Eetplan {

	var $_db;

	function Eetplan(){
		# databaseconnectie openen
		$this->_db=MySql::instance();
	}


	function getEetplan(){
		//huizen laden
		$rEetplan=$this->_db->select("
			SELECT
  			eetplan.uid AS uid,
  			eetplan.huis AS huis,
  			eetplan.avond AS avond
			FROM
				eetplan
			INNER JOIN lid ON(eetplan.uid=lid.uid)
			ORDER BY
 				lid.achternaam, avond;"
			);
		$aEetplan=array();
		$aEetplanRegel=array();
		while($aEetplanData=$this->_db->next($rEetplan)){
			//nieuwe regel beginnen als nodig
			if($aEetplanData['avond']==1){
				$aEetplan[]=$aEetplanRegel;
				$aEetplanRegel=array();
				$lid=LidCache::getLid($aEetplanData['uid']);
				//eerste element van de regel is het uid
				$aEetplanRegel[]=array(
					'uid' => $aEetplanData['uid'],
					'naam' => (string)$lid);
			}
			$aEetplanRegel[]=$aEetplanData['huis'];
		}
		//ook de laaste regel toevoegen
		$aEetplan[]=$aEetplanRegel;
		//eerste regel eruit slopen, die is toch nutteloos.
		unset($aEetplan[0]);
		return $aEetplan;
	}

	function getEetplanVoorPheut($iPheutID){
		$sEetplanQuery="
			SELECT DISTINCT
				eetplan.avond AS avond,
				eetplanhuis.id AS huisID,
				eetplanhuis.naam AS huisnaam,
				eetplanhuis.adres AS huisadres,
				eetplanhuis.groepid AS groepid,
				eetplanhuis.telefoon AS telefoon
			FROM
				eetplanhuis, eetplan
			WHERE
				eetplan.huis=eetplanhuis.id AND
				eetplan.uid='".$iPheutID."'
			ORDER BY
				eetplan.avond;";
		$rEetplanVoorPheut=$this->_db->select($sEetplanQuery);
		if($this->_db->numRows($rEetplanVoorPheut)==0){
			//deze feut bestaat niet
			return false;
		}else{
			$aEetplan=array();
			while($aEetplanData=$this->_db->next($rEetplanVoorPheut)){
				$aEetplan[]=$aEetplanData;
			}
			return $aEetplan;
		}
	}

	function getEetplanVoorHuis($iHuisID){
		$sEetplanQuery="
			SELECT DISTINCT
				eetplan.avond AS avond,
				eetplanhuis.naam AS huisnaam,
				eetplanhuis.adres AS huisadres,
				eetplanhuis.telefoon AS huistelefoon,
				eetplanhuis.groepid AS groepid,
				eetplan.uid AS pheut,
				lid.eetwens AS eetwens,
				lid.telefoon AS telefoon,
				lid.mobiel AS mobiel
			FROM
				eetplanhuis, eetplan
			INNER JOIN lid ON(eetplan.uid=lid.uid)
			WHERE
				eetplan.huis=eetplanhuis.id AND
				eetplanhuis.id=".$iHuisID."
			ORDER BY
				eetplan.avond;";
		$rEetplanVoorHuis=$this->_db->select($sEetplanQuery);
		if($this->_db->numRows($rEetplanVoorHuis)==0){
			//geen huis met dit ID
			return false;
		}else{
			$aEetplan=array();
			while($aEetplanData=$this->_db->next($rEetplanVoorHuis)){
				$aEetplan[]=$aEetplanData;
			}
			return $aEetplan;
		}
	}


	function getDatum($iAvond){
		$aAvonden=array(
			'29-9-2008',
			'27-10-2008',
			'24-11-2008',
			'19-1-2009',
			'??-2-2009',
			'16-3-2009',
			'13-4-2009',
			'16-5-2009');
		return $aAvonden[$iAvond-1];
	}

	function getHuizen(){
		$sHuizenQuery="
			SELECT DISTINCT
				id AS huisID,
				naam AS huisNaam,
				groepid,
				adres,
				telefoon
			FROM
				eetplanhuis
			ORDER BY
				id;";
		$rHuizen=$this->_db->select($sHuizenQuery);
		while($aHuizenData=$this->_db->next($rHuizen)){
			$aHuizen[]=$aHuizenData;
		}
		return $aHuizen;
	}
	function getPheutNaam($uid){
		$lid=LidCache::getLid($uid);
		return (string)$lid;
	}

}
?>
