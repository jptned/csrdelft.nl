{if $album->hasFotos()}
	<script type="text/javascript" src="/layout/js/jquery/plugins/jgallery.js?v=1.4.1"></script>
	<script type="text/javascript">
		try {
			$(function () {
				$("#gallery").jGallery({
					"mode": "slider",
					"width": "750px",
					"slideshowInterval": "6s",
					"autostartAtImage": randomIntFromInterval(0, $('#gallery img').length - 1),
					//"slideshowRandom": true,
					"hideThumbnailsOnInit": true,
					"transition": "random",
					"transitionBackward": "random",
					"transitionCols": "1",
					"transitionRows": "1",
					"backgroundColor": "FFFFFF",
					"textColor": "000000"
				});
			});
		}
		catch (err) {
			// Missing js file
		}
	</script>
	<div id="gallery">
		{foreach from=$album->getFotos() item=foto}
			<img src="{$foto->getResizedUrl()}" />
		{/foreach}
	</div>
{/if}