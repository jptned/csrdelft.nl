<div id="zijbalk_agenda">
	<h1><a href="/actueel/agenda/" title="Agenda">Agenda</a></h1>
	{foreach from=$items item=item}
		{if $item instanceof Lid}
			{* geen verjaardagen hier. *}
		{else}
			<div class="item">
			{if date('d-m', $item->getBeginMoment()) === date('d-m')}
				{$item->getBeginMoment()|date_format:"%H:%M"}
			{elseif strftime('%U', $item->getBeginMoment()) === strftime('%U')}
				<div style="display: inline-block; width: 20px;">{$item->getBeginMoment()|date_format:"%a"}</div>{$item->getBeginMoment()|date_format:"%d"}
			{else}
				{$item->getBeginMoment()|date_format:"%d"}<div style="display: inline-block; width: 6px; text-align: right;">-</div>{$item->getBeginMoment()|date_format:"%m"}
			{/if}
				<a href="/actueel/agenda/maand/{$item->getBeginMoment()|date_format:"%Y-%m"}/#dag-{$item->getBeginMoment()|date_format:"%Y-%m-%d"}" title="{if
				$item|is_a:'\Taken\MLT\Maaltijd' or $item|is_a:'\Taken\CRV\CorveeTaak'}{$item->getTitel()|unescape:"html"|ubb|strip_tags}{else}{$item->getTitel()|ubb|strip_tags}{/if}">
				{if	$item|is_a:'\Taken\MLT\Maaltijd' or $item|is_a:'\Taken\CRV\CorveeTaak'}{$item->getTitel()|unescape:"html"|ubb|strip_tags|truncate:25:"…":true}{else}{$item->getTitel()|ubb|strip_tags|truncate:25:"…":true}{/if}
			</a>
		</div>
		{/if}
	{/foreach}
</div>
