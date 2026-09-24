import "@tsjippy/nonce_script";

//Load after page load
document.addEventListener("DOMContentLoaded", function () {
  if (window["statisticsSent"] == undefined) {
    window["statisticsSent"] = true;

    sendStatistics();
  }
});

//Hide or show the clicked tab
window.addEventListener("hashchange", function () {
  //send statistics
  sendStatistics();
});

function sendStatistics() {
  var formData = new FormData();
  
  const data   = JSON.parse(
    document.getElementById(
        'wp-script-module-data-@tsjippy/nonce_script'
    ).textContent
  );

  formData.append("url", window.location.href);
  formData.append("_wpnonce", data.restNonce);

  fetch(
    `${data.baseUrl}/wp-json${data.restApiPrefix}/statistics/add_page_view`,
    {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    },
  );
}
