class tabIDClass {

    constructor(historyid) {
        console.log(historyid);
        this.setCookie('tabID', this.tabID(), { secure: true, 'max-age': 3600 });
        this.ajax(this.tabID(), historyid);
    }

    setCookie(name, value, options = {}) {

        options = {
            path: '/',
            // ...
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }

    tabID() {
        return sessionStorage.tabID ?
            sessionStorage.tabID :
            sessionStorage.tabID = Math.random()
    }

    historyId() {
        let req = new XMLHttpRequest();
        req.open('GET', document.location, true);
        req.send(null);
        req.onload = function () {
            return headers = req.getResponseHeader('History');
        };
    }

    ajax($tab, historyId) {
        fetch('/tabid?tabid=' + $tab + '&historyid=' + historyId)
            .then(response => {
                return response.text();
            })
            .then(data => {
                // console.log(data);
            });
    }
}

let req = new XMLHttpRequest();
req.open('GET', document.location, true);
req.send(null);
req.onload = function () {
    new tabIDClass(req.getResponseHeader('History'));
};