
function hCreateCookie(name, value, lifetime, dom) {
    var expires = "";
    if (lifetime) {
        var date = new Date();
        date.setTime(date.getTime() + 1000*lifetime);
        expires = " expires=" + date.toGMTString() + ";";
    }
    var domain = "";
    if (dom) {
        domain = " domain=" + dom + ";";
    }

    document.cookie = name + "=" + value + ";" + expires + " path=/;" + domain + ";";
}

//function hReadCookie(name) {
//    var nameEQ = name + "=";
//    var ca = document.cookie.split(";");
//    for (var i = 0; i < ca.length; i++) {
//        var c = ca[i];
//        while (c.charAt(0) == " ") {
//            c = c.substring(1, c.length);
//        }
//        if (c.indexOf(nameEQ) == 0) {
//            return c.substring(nameEQ.length, c.length);
//        }
//    }
//
//    return null;
//}

//function hDeleteCookie(name) {
//    createCookie(name, "", -1);
//}

function initEcondaCookie() {
    if ("undefined" != (typeof econda)) {

        var request = new XMLHttpRequest()
        request.open('GET', `?cl=makaira_connect_econda`, true)

        request.onload = () => {
            if (request.status >= 200 && request.status < 400) {

                var makEcondaAccountId = request.responseText;
                var makEcondaRequest = new econda.recengine.Request({accountId: makEcondaAccountId});
                var makEcondaParams = makEcondaRequest.getRecommendationServiceParameters();

                var makCookiePrefix = "mak";
                var makCookieName = "econda_session";
                var makCookieValue = JSON.stringify(makEcondaParams)
                var makCookieLifetime = 86400;
                var makCookieDomain = ".";

                hCreateCookie(
                    makCookiePrefix + "_" + makCookieName,
                    makCookieValue,
                    makCookieLifetime,
                    makCookieDomain
                );

            } else {
                // We reached our target server, but it returned an error
                console.error('Processing in Makaira failed')
            }
        }

        request.onerror = () => {
            // There was a connection error of some sort
            console.error('Connection to Makaira failed')
        }

        request.send()
    }
}

initEcondaCookie();
