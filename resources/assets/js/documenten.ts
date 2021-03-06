import $ from 'jquery';
import render from './datatable/render';
import CellMetaSettings = DataTables.CellMetaSettings;

/**
 * Documentenketzerjavascriptcode.
 */
$(() => {
	let $documenten = $('#documenten');

	//tabellen naar zebra converteren.
	$documenten.find('tr:odd').addClass('odd');
	// render de filesize cellen
	$documenten.find('.size').each((i, el) => el.innerText = render.filesize(el.innerText, 'display', null, <CellMetaSettings>{}));

	$('#documentencategorie').DataTable({
		language: {
			zeroRecords: 'Geen documenten gevonden',
			infoEmpty: 'Geen documenten gevonden',
			search: 'Zoeken:',
		},
		pageLength: 20,
		info: false,
		lengthChange: false,
		order: [[3, 'desc']], // moment toegevoegd
		columns: [
			{type: 'html'}, // documentnaam
			{type: 'num', render: render.filesize}, // Bestandsgrootte
			{type: 'string'}, // mime-type, forceer string anders werkt sorteren uberhaupt niet
			{render: render.timeago}, //moment toegevoegd
			{}, // Eigenaar
		],
	});
});
