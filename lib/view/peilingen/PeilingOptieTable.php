<?php

namespace CsrDelft\view\peilingen;
use CsrDelft\model\peilingen\PeilingOptiesModel;
use CsrDelft\view\datatable\DataTable;
use CsrDelft\view\datatable\knoppen\ConfirmDataTableKnop;
use CsrDelft\view\datatable\knoppen\DataTableKnop;
use CsrDelft\view\datatable\Multiplicity;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 30/10/2018
 */
class PeilingOptieTable extends DataTable
{
	public function __construct($id)
	{
		parent::__construct(PeilingOptiesModel::ORM, '/peilingen/opties/' . $id, null);

		$this->hideColumn('peiling_id');

		$this->searchColumn('titel');
		$this->searchColumn('beschrijving');

		$this->addKnop(new DataTableKnop(Multiplicity::Zero(), '/peilingen/opties/' . $id . '/toevoegen', 'Toevoegen', 'Optie toevoegen', 'add'));
		$this->addKnop(new ConfirmDataTableKnop(Multiplicity::One(), '/peilingen/opties/' . $id . '/verwijderen', 'Verwijderen', 'Optie verwijderen', 'delete'));
	}

}
