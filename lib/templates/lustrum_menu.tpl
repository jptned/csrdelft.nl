<div id="menu" onmouseover="ResetTimer()" onmouseout="StartTimer()">
	<div id="menuleft"><a href="/"><img src="http://plaetjes.csrdelft.nl/lustrum/logo.png" alt="Logo" id="logo" /></a></div>
	<div id="menucenter">
		<div id="menubanner">
			<div id="banner1"><img src="http://plaetjes.csrdelft.nl/menubalk/banner1.png" id="imgbanner1" alt="banner1" width="553" height="106" /></div>
			<div id="banner2"><img src="http://plaetjes.csrdelft.nl/menubalk/banner2.png" id="imgbanner2" alt="banner2" width="553" height="106" /></div>
			<div id="banner3"><img src="http://plaetjes.csrdelft.nl/menubalk/banner3.png" id="imgbanner3" alt="banner3" width="553" height="106" /></div>
			<div id="banner4"><img src="http://plaetjes.csrdelft.nl/menubalk/banner4.png" id="imgbanner4" alt="banner4" width="553" height="106" /></div>
		</div>
		<ul id="mainmenu">
			{foreach from=$items item=item}
				<li>
					<a href="{$item.link}" id="top{$item.ID}" onmouseover="StartShowMenu('{$item.ID}');" onmouseout="ResetShowMenu();" {if $item.huidig}class="active" {/if}title="{$item.tekst}">{$item.tekst}</a>
					{if $item.huidig}
						<script type="text/javascript">
							SetActive({$item.ID});
							document.getElementById('banner'+{$item.ID}).style.display = "inline";
							fixPNG('imgbanner1')
						</script>
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
	<div id="menuright">
		{if $loginlid->hasPermission('P_LOGGED_IN') }
			<div id="ingelogd">
				<a href="/instellingen/" class="instellingen" title="Webstekinstellingen">{icon get="instellingen"}</a>
				{if $loginlid->isSued()}
					<a href="/endsu/" style="color: red;">{$loginlid->getSuedFrom()->getNaamLink('civitas','html')} als</a><br />»
				{/if}
				{$loginlid->getUid()|csrnaam}<br />
				<div id="uitloggen"><a href="/logout.php">log&nbsp;uit</a></div>
				<div id="saldi">
					{foreach from=$loginlid->getLid()->getSaldi() item=saldo}
						<div class="saldoregel">
							<div class="saldo{if $saldo.saldo < 0 AND $loginlid->getUid()!='0524'} staatrood{/if}">&euro; {$saldo.saldo|number_format:2:",":"."}</div>
							{$saldo.naam}:
						</div>
					{/foreach}
				</div>
				{if $loginlid->hasPermission('P_LEDEN_MOD')}
				<div id="adminding">
					Beheer
					{if $loginlid->hasPermission('P_ADMIN')}
						{if $queues.forum->count()>0 OR $queues.meded->count()>0}
							({$queues.forum->count()}/{$queues.meded->count()})
						{/if}
					{/if}
					<div>
						{if $loginlid->hasPermission('P_ADMIN')}
						<span class="queues">
							{foreach from=$queues item=queue key=name}
								<a href="/tools/query.php?id={$queue->getID()}">
									{$name|ucfirst}: <span class="count">{$queue->count()}</span><br />
								</a>
							{/foreach}
						</span>
						<a href="/su/x101">&raquo; SU Jan Lid.</a><br />
						{/if}
						<a href="/pagina/beheer">&raquo; Beheeroverzicht</a><br />
						<a href="/tools/query.php">&raquo; Opgeslagen queries</a><br />
						<br />
						<a href="/actueel/maaltijden/beheer/">&raquo; Maaltijdbeheer</a><br />
					</div>
				</div>
				{literal}
				<script>
					jQuery(document).ready(function($){
						
						$('#adminding').click(function(){
							$(this).children('div').toggle();
						});
						$('#adminding div').hide();
					});
				</script>
				{/literal}
				{/if}
				<br />
				<form method="get" action="/communicatie/lijst.php" name="lidzoeker">
					<p>
						{if isset($smarty.get.q)}
							<input type="text" value="{$smarty.get.q|escape:'htmlall'}" name="q" id="zoekveld" />
						{else}
							<input type="text" name="q" id="zoekveld" />
						{/if}
					</p>
				</form>
			</div>
		{else}
			<div id="key"><img src="http://plaetjes.csrdelft.nl/layout/key.png" onclick="toggleDiv('login')" alt="Inloggen" /></div>
			<div id="login">
				{if isset($smarty.session.auth_error)}
					<span class="waarschuwing">{$smarty.session.auth_error}</span>
				{/if}
				<form action="/login.php" method="post">
					<fieldset>
						<input type="hidden" name="url" value="{$smarty.server.REQUEST_URI}" />
						<input type="text" name="user" value="naam" onfocus="if(this.value=='naam')this.value='';" />
						<input type="password" name="pass" value="" />
						<input type="checkbox" name="checkip" class="checkbox" value="true" id="login-checkip" />
						<label for="login-checkip">Koppel IP</label>
						<input type="submit" class="submit" name="submit" value="Inloggen" />
					</fieldset>
				</form>
			</div>
			{if !isset($smarty.session.auth_error)}
				<script type="text/javascript">hideDiv(document.getElementById('login'));</script>
			{/if}
		{/if}
	</div>
</div>

<div id="submenu" onmouseover="ResetTimer()" onmouseout="StartTimer()">
	<div id="submenuitems">
		{foreach from=$items item=item}
			<div id="sub{$item.ID}"{if $item.huidig} class="active"{/if}>
				{assign var='showseperator' value=false}
				{foreach from=$item.subitems item=subitem}
					{if $showseperator}<img src="http://plaetjes.csrdelft.nl/lustrum/spacer.png" alt="|" /> {/if}
					{assign var='showseperator' value=true}
					<a href="{$subitem.link}" title="{$subitem.tekst}"{if $subitem.huidig} class="active"{/if}>{$subitem.tekst}</a>
				{/foreach}
			</div>
		{/foreach}
	</div>
</div>
