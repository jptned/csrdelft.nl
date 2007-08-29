<?php
# C.S.R. Delft | pubcie@csrdelft.nl
# -------------------------------------------------------------------
# class.bestuur.php
# -------------------------------------------------------------------

class Bestuur{

	### private ###

	# de objecten die data leveren
	private $_db;
	private $_lid;

	private $_jaar='';
	private $_aBestuur='';
	
	function Bestuur() {
		$this->_lid=Lid::get_lid();
		$this->_db=MySql::get_MySql();
	}
	
	//kijk of er een bestuur ingeladen is, anders het huidige inladen.
	function loadIfNot(){
		if(!isset($this->_aBestuur['naam'])){
			//kennelijk nog niets geladen, dan nu maar doen.
			$this->loadBestuur();	
		}
	}
	
	function loadBestuur($jaar=0){
		//leeggooien
		$this->_jaar=0;
		$this->_aBestuur=array();
		
		$jaar=(int)$jaar;
		if($jaar==0){
			//huidige bestuur laden...
			$jaar=date('Y');
			if(date('m-d')<'06-28') $jaar-=1;
		}else{
			if(!preg_match('/(19|20)\d{2}/', $jaar)) return false;
			$this->_jaar=$jaar;
		}
		$sBestuur="
			SELECT
				ID, jaar, naam, 
				praeses, abactis, fiscus, vice_praeses, vice_abactis, 
				verhaal, tekst
			FROM
				bestuur
			WHERE
				jaar=".$jaar."
			LIMIT 1;";

		$rBestuur=$this->_db->query($sBestuur);
		if($rBestuur===false){ 
			return $this->loadBestuur($jaar-1); 
		}else{
			$this->_aBestuur=$this->_db->next($rBestuur);
			return true;
		}
	}
	//check of de huidige gebruiker of $uid in het bestuur zit.
	function isBestuur($uid=''){
		$this->loadIfNot();
		if($uid==''){ $uid=$this->_lid->getUid(); }
		return in_array($uid, $this->_aBestuur); 
	}
	function getBestuur(){ 
		$this->loadIfNot();
		return $this->_aBestuur;	
	}
	
	//regel een lijst met besturen die zich in de database bevinden
	function getBesturen(){
		$sBesturen="
			SELECT 
				jaar, naam, praeses
			FROM
				bestuur
			ORDER BY
				jaar DESC";
		$rBesturen=$this->_db->query($sBesturen);
		if($rBesturen!==false AND $this->_db->numRows($rBesturen)!=0){
			return $this->_db->result2array($rBesturen);
		}else{
			return false;
		}
	}	
	
}

?>
