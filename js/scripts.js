jQuery(document).ready(function() {

	if(!window.location.search.length) return;

	if(window.location.search.match("efd_download").length){
		$('#efd_force_download').submit();
	}

});