[{if $oViewConf->isEcondaActive() }]
    [{oxscript include=$oViewConf->getModuleUrl('makaira/connect', 'out/src/scripts/features/econda-recommendations.js') priority=10 }]
    [{oxscript include=$oViewConf->getModuleUrl('makaira/connect', 'out/src/scripts/features/econda.js') priority=20 }]
[{/if}]
