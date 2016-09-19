<?php

require_once 'model/EetplanModel.class.php';
require_once 'view/EetplanView.class.php';

/**
 * EetplanController.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Controller voor eetplan.
 */
class EetplanController extends AclController {
    /**
     * @var EetplanModel
     */
    protected $model;
    private $lichting;

    public function __construct($query) {
        parent::__construct($query, EetplanModel::instance());
        if ($this->isPosted()) {
            $this->acl = array(
                'beheer' => 'P_ADMIN',
                'woonoorden' => 'P_ADMIN',
                'novietrelatie' => 'P_ADMIN',
                'bekendehuizen' => 'P_ADMIN',
                'nieuw' => 'P_ADMIN'
            );
        } else {
            $this->acl = array(
                'view' => 'P_LEDEN_READ',
                'noviet' => 'P_LEDEN_READ',
                'huis' => 'P_LEDEN_READ',
                'beheer' => 'P_ADMIN',
                'bekendehuizen' => 'P_ADMIN',
                'json' => 'P_LEDEN_READ',
            );
        }
    }

    /**
     * /eetplan/<$this->action>/<$this->lichting>|huidig/<$args>
     *
     * @param array $args
     * @return mixed
     */
	public function performAction(array $args = array()) {
		$this->action = 'view';
		if ($this->hasParam(2)) {
			$this->action = $this->getParam(2);
		}

		if ($this->hasParam(3) AND !($this->getParam(3) == 'huidig')) {
		    $this->lichting = $this->getParam(3);
        } else {
            $this->lichting = substr((string) LichtingenModel::getJongsteLidjaar(), 2, 2);
        }

		return parent::performAction($this->getParams(4));
	}

	public function view() {
		$body = new EetplanView($this->model, $this->lichting);
		$this->view = new CsrLayoutPage($body);
        $this->view->addCompressedResources('eetplan');
	}

	public function noviet($uid = null) {
	    $eetplan = $this->model->getEetplanVoorNoviet($uid);
        if ($eetplan === false) {
            $this->geentoegang();
        }
		$body = new EetplanNovietView($this->model, $this->lichting, $uid);
		$this->view = new CsrLayoutPage($body);
	}

	public function huis($id = null) {
	    $eetplan = $this->model->getEetplanVoorHuis($id, $this->lichting);
        if ($eetplan === false) {
            $this->geentoegang();
        }
		$body = new EetplanHuisView($this->model, $this->lichting, $id);
		$this->view = new CsrLayoutPage($body);
	}

    public function woonoorden($actie = null) {
        if ($actie == 'aan' OR $actie == 'uit') {
            $selection = filter_input(INPUT_POST, 'DataTableSelection', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
            $woonoorden = array();
            foreach ($selection as $woonoord) {
                $woonoord = WoonoordenModel::instance()->retrieveByUUID($woonoord);
                $woonoord->eetplan = $actie == 'aan';
                WoonoordenModel::instance()->update($woonoord);
                $woonoorden[] = $woonoord;
            }
            $this->view = new EetplanHuizenView($woonoorden);
        } else {
            $woonoorden = WoonoordenModel::instance()->find('status = ?', array(GroepStatus::HT));
            $this->view = new EetplanHuizenView($woonoorden);
        }
    }

    public function bekendehuizen($actie = null) {
        if ($this->isPosted()) {
            if ($actie == 'toevoegen') {
                $eetplan = new Eetplan();
                $eetplan->avond = '0000-00-00';
                $form = new EetplanBekendeHuizenForm($eetplan);
                if ($form->validate()) {
                    $this->model->create($eetplan);
                    $this->view = new EetplanBekendeHuizenResponse($this->model->getBekendeHuizen($this->lichting));
                } else {
                    $this->view = $form;
                }
            } elseif ($actie == 'verwijderen') {
                $selection = filter_input(INPUT_POST, 'DataTableSelection', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
                $verwijderd = array();
                foreach ($selection as $uuid) {
                    $eetplan = $this->model->retrieveByUUID($uuid);
                    $this->model->delete($eetplan);
                    $verwijderd[] = $eetplan;
                }
                $this->view = new RemoveRowsResponse($verwijderd);
            } else {
                $this->view = new EetplanBekendeHuizenResponse($this->model->getBekendeHuizen($this->lichting));
            }
        } else {
            if ($actie == 'zoeken') {
                $huisnaam = filter_input(INPUT_GET, 'q');
                $huisnaam = '%' . $huisnaam . '%';
                $woonoorden = WoonoordenModel::instance()->find('status = ? AND naam LIKE ?', array(GroepStatus::HT, $huisnaam))->fetchAll();
                $this->view = new EetplanHuizenResponse($woonoorden);
            } else {
                $this->geentoegang();
            }
        }
    }


    public function novietrelatie($actie = null) {
        $model = EetplanBekendenModel::instance();
        if ($actie == 'toevoegen') {
            $form = new EetplanBekendenForm(new EetplanBekenden());
            if ($form->validate()) {
                $model->create($form->getModel());
                $this->view = new EetplanRelatieView($model->getBekenden($this->lichting));
            } else {
                $this->view = $form;
            }
        } elseif ($actie == 'verwijderen') {
            $selection = filter_input(INPUT_POST, 'DataTableSelection', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
            $verwijderd = array();
            foreach ($selection as $uuid) {
                $bekenden = $model->retrieveByUUID($uuid);
                EetplanBekendenModel::instance()->delete($bekenden);
                $verwijderd[] = $bekenden;
            }
            $this->view = new RemoveRowsResponse($verwijderd);
        } else {
            $this->view = new EetplanRelatieView($model->getBekenden($this->lichting));
        }
    }

    /**
     * Beheerpagina.
     *
     * POST een json body om dingen te doen.
     */
    public function beheer() {
        $body = new EetplanBeheerView($this->model, $this->lichting);
        $this->view = new CsrLayoutPage($body);
        $this->view->addCompressedResources('eetplan');
    }

    public function json() {
        $eetplan = $this->model->getEetplan($this->lichting);
        $this->view = new JsonResponse($eetplan);
    }

    public function nieuw() {
        $form = new NieuwEetplanForm();

        if ($form->validate()) {
            $avond = filter_input(INPUT_POST, 'avond', FILTER_SANITIZE_STRING);
            $eetplan = $this->model->maakEetplan($avond, $this->lichting);

            echo "Aantal: " . count($eetplan) . "\n";

            foreach ($eetplan as $sessie) {
                //$this->model->create($sessie);
                echo "uid: " . $sessie->uid . "\n";
                echo "woonoord: " . $sessie->woonoord_id . "\n\n";
            }
        } else {
            $this->view = $form;
        }
    }
}
