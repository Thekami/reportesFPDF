$(document).ready(function(){

	
})


$(document).on('click', '#btn_reporte', function(e){

	var filtro = $(this).attr('class');
	filtro = filtro.split('reporte_')[1];

	if (filtro == "activos")
		var x = 1;
	else
		var x = 0;
	
	window.open('server/pdf_implementa.php?x='+x);
});