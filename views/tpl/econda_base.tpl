[{if $oViewConf->isEcondaActive() }]
    <script type="text/javascript" defer="defer" src="[{$oViewConf->getEcondaLoaderUrl()}]" client-key="[{$oViewConf->getEcondaClientKey()}]" container-id="[{$oViewConf->getEcondaContainerId()}]"></script>
    <script type="text/javascript" src="[{$oViewConf->getModuleUrl('makaira/connect', 'out/src/scripts/features/econda.js')}]"></script>
[{/if}]
