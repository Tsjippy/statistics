document.addEventListener("click", (ev) => {
    if(event.target.matches(`.exclude-url`)){
        let form = document.getElementById('statistics-overview-settings');
        let excludeList = form.querySelector('#exclude-list');
        if (excludeList.value != '') {
            excludeList.value = excludeList.value + ',' + el.value;
        } else {
            excludeList.value = el.value;
        }

        form.submit();
    }
});

