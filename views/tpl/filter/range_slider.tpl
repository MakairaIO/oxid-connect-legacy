[{assign var=fromname value='makairaFilter['|cat:$aggregation->key|cat:"_from]"}]
[{assign var=toname value='makairaFilter['|cat:$aggregation->key|cat:"_to]"}]
[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]
[{assign var=fCurrencyRate value=$oActCurrency->rate}]
[{assign var=sSliderId value="slider-range-"|cat:$aggregation->key}]
[{assign var=sLabelId value="slider-range-label-"|cat:$aggregation->key}]

<input type="hidden" name="[{$fromname}]" value="[{$facetParams.$fromname}]" />
<input type="hidden" name="[{$toname}]" value="[{$facetParams.$toname}]" />

[{capture}]
    <script>
    [{capture assign="filterScript"}]
        (function($) {
            var redirecturl='[{ $oViewConf->getFilterSeoLink($baseLink,$aggregation->key) }]';
            var leftvalue='[{$facetParams.$fromname}]';
            var rightvalue='[{$facetParams.$toname}]';
            var currencysign='[{$sCurrencySign}]';
            var currencyrate='[{$fCurrencyRate}]';
            var separator='&';
            var step=1;
            if (leftvalue=='') {leftvalue=[{$aggregation->min|floor}]}
            if (rightvalue=='') {rightvalue=[{$aggregation->max|ceil}]}
            $( "#[{$sSliderId}]" ).slider({
                range: true,
                step: step,
                min: [{$aggregation->min|floor}],
                max: [{$aggregation->max|ceil}],
                values: [ leftvalue, rightvalue ],
                slide: function( event, ui ) {
                    $( "#[{$sLabelId}]" ).html( currencysign + Math.floor(ui.values[ 0 ]*currencyrate) + " - " + currencysign + Math.ceil(ui.values[ 1 ]*currencyrate) );

                    if (ui.values[ 1 ] == leftvalue)
                    {
                        $( "[{$sLabelId}]" ).html( currencysign + Math.floor(ui.values[ 0 ]*currencyrate) + " - " + currencysign + Math.ceil((ui.values[ 1 ] + step)*currencyrate) );
                        $( "#[{$sSliderId}]" ).slider( "values", 1 ) = ui.values[ 0 ] + step;
                    }
                    if (ui.values[ 0 ] == rightvalue)
                    {
                        $( "[{$sLabelId}]" ).html( currencysign + Math.floor((ui.values[ 0 ] - step)*currencyrate) + " - " + currencysign + Math.ceil(ui.values[ 1 ]*currencyrate));
                        $( "#[{$sSliderId}]" ).slider( "values", 0 ) = ui.values[ 1 ] - step;
                    }
                },
                change: function(event, ui) {
                    var found=redirecturl.indexOf('?');
                    if (found==-1){separator='?'}

                    [{* ugly hack for decoding encoded url in javascript *}]
                    var div = document.createElement('div');
                    div.innerHTML = redirecturl;
                    var redirecturl_decoded = div.firstChild.nodeValue;

                    window.location.assign(redirecturl_decoded+separator+'[{$aggregation->key}]_from='+$( "#[{$sSliderId}]" ).slider( "values", 0 )+'&[{$aggregation->key}]_to='+$( "#[{$sSliderId}]" ).slider( "values", 1 ));
                }
            });
            $( "#[{$sLabelId}]" ).html( currencysign + Math.floor($( "#[{$sSliderId}]" ).slider( "values", 0 )*currencyrate) +
            " - "+ currencysign + Math.ceil($( "#[{$sSliderId}]" ).slider( "values", 1 )*currencyrate) );
        })(jQuery);
    [{/capture}]
    </script>
[{/capture}]
[{oxscript add=$filterScript}]
<span id="[{$sLabelId}]" style="border:0; color:#f6931f; font-weight:bold;"></span>
<div id="[{$sSliderId}]"></div>
