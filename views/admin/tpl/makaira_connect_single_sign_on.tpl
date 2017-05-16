[{if !$hasApplicationUrl}]
    <h1>Warning</h1>
    <p>Please configure the Makaira application URL</p>
[{else}]
    <iframe src="[{$iframeUrl}]" width="100%" height="100%" frameborder="0"></iframe>
[{/if}]
