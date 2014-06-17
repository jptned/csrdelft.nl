<div style="float: right; margin: 0 0 10px 10px;">
	{if LoginLid::mag('P_LEDEN_READ')}
		<a class="knop" href="/fotostoevoegen/">{icon get="toevoegen"} Toevoegen</a>
	{/if}
	{if LoginLid::mag('P_LOGGED_IN') && $album->getFotos()!==false}
		<a class="knop" href="/fotoalbum/downloaden{$album->getSubDir()}" title="Download als TAR-bestand">{icon get="folder_picture"} Download album</a>
	{/if}
	{if LoginLid::mag('P_DOCS_MOD')}
		<a class="knop" href="/fotoalbum/verwerken{$album->getSubDir()}">{icon get="photos"} Verwerken</a>
	{/if}
</div>
{$view->getBreadcrumbs($album, true)}
<h1>{$album->mapnaam|ucfirst}</h1>
{foreach from=$album->getSubAlbums() item=subalbum}
	<div class="album hoverIntent">
		<a href="{$subalbum->getUrl()}">
			<img src="{$subalbum->getThumbURL()}" />
			<div id="{$subalbum->mapnaam|md5}" class="albumname">
				{if LoginLid::mag('P_DOCS_MOD')}
					<a href="/fotoalbum/hernoemen{$subalbum->getSubDir()}" class="knop post prompt hoverIntentContent" title="Fotoalbum hernoemen" postdata="Nieuwe naam={$subalbum->mapnaam}" style="position: absolute; top: -90px; left: 118px;">{icon get=pencil}</a>
				{/if}
				{$subalbum->mapnaam}
			</div>
		</a>
	</div>
{/foreach}
{foreach from=$album->getFotos() item=foto}
	<div id="{$foto->filename|md5}" class="thumb hoverIntent">
		{if LoginLid::mag('P_DOCS_MOD')}
			<div style="position: absolute;">
				<a href="/fotoalbum/verwijderen{$album->getSubDir()}{$foto->filename}" class="knop post confirm hoverIntentContent" title="Definitief verwijderen van deze foto">{icon get=cross}</a>
				<a href="/fotoalbum/albumcover{$album->getSubDir()}{$foto->filename}" class="knop confirm hoverIntentContent" title="Instellen als albumcover" style="position: relative; left: 118px;">{icon get=folder_picture}</a>
			</div>
		{/if}
		<a href="{$foto->getResizedURL()}" rel="prettyPhoto[album]">
			<img src="{$foto->getThumbURL()}" />
		</a>
	</div>
{/foreach}
{literal}
<script type="text/javascript">
jQuery(document).ready(function($) {
	$("a[rel^='prettyPhoto']").prettyPhoto({
		theme: 'dark_rounded',
		markup: '<div class="pp_pic_holder"> \
	<div class="ppt">&nbsp;</div> \
	<div class="pp_top"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
	</div> \
	<div class="pp_content_container"> \
		<div class="pp_left"> \
		<div class="pp_right"> \
			<div class="pp_content"> \
				<div class="pp_loaderIcon"></div> \
				<div class="pp_fade"> \
					<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
					<div class="pp_hoverContainer"> \
						<a class="pp_next" href="#">next</a> \
						<a class="pp_previous" href="#">previous</a> \
					</div> \
					<div id="pp_full_res"></div> \
					<div class="pp_details clearfix"> \
						<a id="fullsizeLink"><img src="http://plaetjes.csrdelft.nl/famfamfam/disk.png" title="Origineel downloaden" /></a> \
						<p class="pp_description"></p> \
						<!--<a class="pp_close" href="#">Close</a>--> \
						<div class="pp_nav"> \
							<a href="#" class="pp_arrow_previous">Previous</a> \
							<p class="currentTextHolder">0/0</p> \
							<a href="#" class="pp_arrow_next">Next</a> \
						</div> \
					</div> \
				</div> \
			</div> \
		</div> \
		</div> \
	</div> \
	<div class="pp_bottom"> \
		<div class="pp_left"></div> \
		<div class="pp_middle"></div> \
		<div class="pp_right"></div> \
	</div> \
</div> \
<div class="pp_overlay"></div>',
		changepicturecallback: function() {

			imgurl = $('#fullResImage').attr('src');
			$('#fullsizeLink').attr('href', imgurl.replace('_resized/', ''));

			urlparts = imgurl.split('/');
			window.location.hash = '#' + urlparts[urlparts.length - 1];

		},
		callback: function() {
			return false;
		}
	});

//open de foto waar naar gelinkt wordt met de hashtag.
	if (window.location.hash != '') {
		imgname = window.location.hash.substring(1);

//verzamel alle plaatjes.
		var pics = $('a[rel*=prettyPhoto]');
		var urls = pics.map(function() {
			return $(this).attr('href')
		});
		var titles = pics.map(function() {
			return $(this).attr('title')
		});

		$.prettyPhoto.open(urls, null, titles);

//welke moeten we openen?
		indexoftheimage = 0;
		pics.each(function(index, value) {
			if (pics[index].title == imgname) {
				$.prettyPhoto.changePage(index);
			}
		});
	}
});
</script>
{/literal}