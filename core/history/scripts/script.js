class tabIDClass {
    tab() {
        let tabID = this.tabID();
        console.log(tabID, 'tab');
        this.setCookie('tabID', tabID);
    }
    history() {
        let req = new XMLHttpRequest();
        let h = this;
        req.open('GET', document.location, true);
        req.send(null);
        req.onload = function () {
            let history = req.getResponseHeader('History');
            if (history) {
                console.log(history.toLowerCase(), 'h');
                h.setCookie('History', history.toLowerCase());
            }
        };
    }
    setCookie(name, value, options = {}) {
        options = {
            path: '/',
            secure: true,
            'max-age': 3600,
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
        if (!sessionStorage.tabID) {
            sessionStorage.tabID = Math.floor(Math.random() * 1000000);
        }
        return sessionStorage.tabID;
    }
    historyId() {
        let req = new XMLHttpRequest();
        req.open('GET', document.location, true);
        req.send(null);
        req.onload = function () {
            return headers = req.getResponseHeader('History');
        };
    }
}
new tabIDClass().history();
new tabIDClass().tab();