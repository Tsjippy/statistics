//Load after page load
document.addEventListener("DOMContentLoaded", function() {
	if(window['statisticsSent'] == undefined){
		window['statisticsSent']	= true;
		
		sendStatistics();
	}
});

//Hide or show the clicked tab
window.addEventListener("hashchange", function() {
    //send statistics
	sendStatistics();
});

function sendStatistics(){
    var formData = new FormData();
    formData.append('url', window.location.href);
    formData.append('_wpnonce', restNonce);

	fetch(
		`${baseUrl}/wp-json${restApiPrefix}/statistics/add_page_view`,
		{
			method: 'POST',
			credentials: 'same-origin',
			body: formData
		}
	);
}