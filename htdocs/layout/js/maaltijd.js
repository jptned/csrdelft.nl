/* 
 * maaltijdketzer
 */

jQuery(document).ready(function(){
	//reset knopje maaltijdtoevoegformulier
	corveeVeldResetter();

})

//zet inputs voor corveetaken op nul
function corveeVeldResetter(){
	jQuery(".knop.zetopnul").click(function (event) {
		event.preventDefault();
		jQuery("#corveevelden").children("div").children("input[type=text]").val(0);
	});
} 

//regelt de ajaxrequest als actieknoppen van corveeresetter worden gebruikt
function corveeResetter(actie){
	//data verzamelen
	var data="resetactie="+actie;
	jQuery('#resetForm').children("fieldset").children("select").each(function(index){
		data=data+"&"+jQuery(this).attr("name")+"="+jQuery(this).val();
	});
	//datumveld disablen
	jQuery('#resetForm').children("fieldset").children("select").attr('disabled', 'disabled');
	//actie uitvoeren
	jQuery.ajax({
		type: "POST",
		url: '/actueel/maaltijden/corveeinstellingen/',
		data: data,
		cache: false,
		success: function(response){
			//controleknop terugzetten
			jQuery('#controleContainer').hide();
			jQuery('#resetContainer').html(response);
			jQuery('#resetContainer').show();
			jQuery("#resetcontrolletabel tr:odd").addClass('odd');
		}
	});
}
//zet corveeresetter terug naar datuminvulveld
function restoreCorveeResetter(){
	//enable datumveld
	jQuery('#resetForm').children("fieldset").children("select").removeAttr('disabled');
	//controleknop terugzetten
	jQuery('#controleContainer').show();
	jQuery('#resetContainer').hide();
}
