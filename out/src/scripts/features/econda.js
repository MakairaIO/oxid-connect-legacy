
function hCreateCookie(name, value, lifetime, dom) {
    var expires = "";
    if (lifetime) {
        var date = new Date();
        date.setTime(date.getTime() + 1000 * lifetime);
        expires = " expires=" + date.toGMTString() + ";";
    }
    var domain = "";
    if (dom) {
        domain = " domain=" + dom;
    }

    document.cookie = name + "=" + value + ";" + expires + " path=/;" + domain + ";";
}

function hReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }

    return null;
}

function hDeleteCookie(name) {
    createCookie(name, "", -1);
}

function initEcondaCookie() {
    if ("undefined" != (typeof econda)) {

        var makCookiePrefix    = "mak_";
        var makCookieAccountId = makCookiePrefix + "econda_aid";
        var makCookieSession   = makCookiePrefix + "econda_sid";
        var makCookieLifetime  = 86400;
        var makCookieDomain    = null;
        var makCookieValue     = null;

        var makEcondaAccountId = hReadCookie(makCookieAccountId);

        if (makEcondaAccountId == null) {
            var request = new XMLHttpRequest()
            request.open('GET', `?cl=makaira_connect_econda`, true)

            request.onload = () => {
                if (request.status >= 200 && request.status < 400) {
                    makEcondaAccountId = request.responseText;
                    makCookieValue     = btoa(makEcondaAccountId);

                    hCreateCookie(
                        makCookieAccountId,
                        makCookieValue,
                        makCookieLifetime,
                        makCookieDomain
                    );
                }
            }

            request.onerror = () => {
                // There was a connection error of some sort
            }

            request.send();
        } else {
            makEcondaAccountId = atob(makEcondaAccountId);
        }

        if (makEcondaAccountId != null) {
            var makEcondaRequest = new econda.recengine.Request({accountId: makEcondaAccountId});
            var makEcondaParams  = makEcondaRequest.getRecommendationServiceParameters();
            var makCookieValue   = JSON.stringify(makEcondaParams);

            hCreateCookie(
                makCookieSession,
                makCookieValue,
                makCookieLifetime,
                makCookieDomain
            );
        }
    }
}

initEcondaCookie();
