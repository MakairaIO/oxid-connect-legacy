[{*oxscript include=$oViewConf->getModuleUrl('marm/cookie-banner', 'out/js/dist/main.js')*}]

<div class="makaira-cookie-banner" id="cookieConsentBanner">
    <div class="container-fluid makaira-cookie-banner__container">
        <div class="makaira-cookie-banner__hint">
            <h3>[{oxmultilang ident="MAKAIRA_COOKIE_BANNER_HEADING"}]</h3>
            [{oxmultilang ident="MAKAIRA_COOKIE_BANNER_INFO"}]
        </div>
        <div class="makaira-cookie-banner__buttons">
            <button class="btn btn-primary" data-consent-decision="accept">[{oxmultilang ident="MAKAIRA_COOKIE_BANNER_ACCEPT"}]</button>
            <button class="btn btn-default" data-consent-decision="decline">[{oxmultilang ident="MAKAIRA_COOKIE_BANNER_DECLINE"}]</button>
            <span class="makaira-cookie-banner__buttons-close" data-consent-action=""></span>
        </div>
    </div>
</div>
