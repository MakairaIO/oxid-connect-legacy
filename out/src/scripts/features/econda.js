function hCreateCookie(name, value) {
    const lifetime = 60 * 60 * 365; // 1 year
    let expires = "";
    let date = new Date();
    date.setTime(date.getTime() + 1000 * lifetime);
    expires = " expires=" + date.toGMTString() + ";";

    document.cookie = name + "=" + value + ";" + expires + " path=/;";
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
    hCreateCookie(name, "", -1);
}

function createEcondaSession(makEcondaAccountId) {
    const makCookieSession = "mak_econda_session";
    const makEcondaSession = hReadCookie(makCookieSession);

    if (makEcondaSession) {
        return;
    }

    let makEcondaRequest = new econda.recengine.Request({accountId: makEcondaAccountId});
    let makEcondaParams = makEcondaRequest.getRecommendationServiceParameters();
    const makCookieValue = JSON.stringify(makEcondaParams);

    hCreateCookie(makCookieSession, makCookieValue);
}

function initEcondaCookie() {
    if ("undefined" === typeof econda) {
        hDeleteCookie("mak_econda_session");
        return;
    }

    const makCookieAccountId = "mak_econda_aid";
    let makCookieValue = null;

    let makEcondaAccountId = hReadCookie(makCookieAccountId);

    if (makEcondaAccountId == null) {
        var request = new XMLHttpRequest()
        request.open('GET', `?cl=makaira_connect_econda`, true);

        request.onload = () => {
            if (request.status >= 200 && request.status < 400) {
                makEcondaAccountId = request.responseText;
                makCookieValue = btoa(makEcondaAccountId);

                hCreateCookie(makCookieAccountId, makCookieValue);
                createEcondaSession(makEcondaAccountId);
            }
        }

        request.onerror = () => {
            // There was a connection error of some sort
        }

        request.send();
    } else {
        makEcondaAccountId = atob(makEcondaAccountId);
        createEcondaSession(makEcondaAccountId);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(initEcondaCookie, 500);
});