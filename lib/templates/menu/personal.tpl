<li class="has-children{if LoginModel::instance()->isSued()} sued{/if}">
	<a href="#0">{LoginModel::getUid()|csrnaam:"civitas":"plain"}</a>
	<ul class="is-hidden">
		<li class="go-back"><a href="#0">{LoginModel::getUid()|csrnaam:"civitas":"plain"}</a></li>
{if LoginModel::instance()->isSued()}
		<li><a href="/endsu/" class="error" title="Switch user actie beeindingen">SU {LoginModel::instance()->getSuedFrom()->getNaamLink('civitas', 'plain')}</a></li>
{/if}
		<li><a href="/profiel/{LoginModel::getUid()}" title="Ga naar mijn profiel">Profiel</a></li>
		<li>
			<a href="/profiel/{LoginModel::getUid()}#SocCieSaldo" title="Bekijk SocCie saldo historie">
				{assign var=saldo value=LoginModel::instance()->getLid()->getSoccieSaldo()}
				SocCie: <span{if $saldo < 0} class="staatrood"{/if}>&euro; {$saldo|number_format:2:",":"."}</span>
			</a>
		</li>
		<li>
			<a href="/profiel/{LoginModel::getUid()}#MaalCieSaldo" title="Bekijk MaalCie saldo historie">
				{assign var=saldo value=LoginModel::instance()->getLid()->getMaalcieSaldo()}
				MaalCie: <span{if $saldo < 0} class="error"{/if}>&euro; {$saldo|number_format:2:",":"."}</span>
			</a>
		</li>
		<li class="has-children">
			<a href="#0">{$favorieten->tekst}</a>
			<ul class="is-hidden">
				<li class="go-back"><a href="#0">{$favorieten->tekst}</a></li>
				{include file='menu/main_tree.tpl' parent=$favorieten}
			</ul>
		</li>
		<li><a href="/menubeheer/toevoegen/favoriet" class="post popup addfav" onclick="$('.cd-nav-trigger').click();" title="Huidige pagina toevoegen aan favorieten">Favoriet toevoegen</a></li>
		{include file='menu/main_tree.tpl' parent=$item}
{if LoginModel::mag('P_LEDEN_MOD')}
	{if isset($forumcount)}
		<li><a href="/forum/wacht" title="Aantal forumberichten dat wacht op goedkeuring">Forum: {$forumcount}</a></li>
	{/if}
	{foreach from=$queues item=queue key=name}
		<li><a href="/tools/query.php?id={$queue->getID()}" title="Aantal {$name} dat wacht op goedkeuring">{$name|ucfirst}: {$queue->count()}</a></li>
	{/foreach}
{/if}
		<li><a href="/logout" title="Uitloggen">Log uit</a></li>
	</ul>
</li>