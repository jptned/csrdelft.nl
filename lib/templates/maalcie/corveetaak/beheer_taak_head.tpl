{*
	beheer_taak_head.tpl	|	P.W.G. Brussee (brussee@live.nl)
*}
<tr{if isset($datum)} id="taak-datum-head-{$datum}" class="taak-datum-head taak-datum-{$datum}{if !isset($show)} verborgen" onclick="taken_toggle_datum('{$datum}');{/if}"{/if}>
	<th style="width: 80px;">Wijzig</th>
	<th>Gemaild</th>
	<th style="width: 60px;">Datum</th>
	<th>Functie</th>
	<th>Lid</th>
	<th>Punten<br />toegekend</th>
	<th class="center-text">{if $prullenbak}{icon get="cross" title="Definitief verwijderen"}{else}{icon get="bin_empty" title="Naar de prullenbak verplaatsen"}{/if}</th>
</tr>