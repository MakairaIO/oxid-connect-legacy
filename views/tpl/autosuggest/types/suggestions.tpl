<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--header">Suchvorschläge</li>

[{foreach from=$suggestions item=suggestion}]
    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--suggestion">
        <a href="[{$oViewConf->getSelfActionLink()|cat:"cl=search&searchparam="|cat:$suggestion.label}]" class="makaira-autosuggestion__link">
            <span class="makaira-autosuggestion__title">[{$suggestion.label}]</span>
        </a>
    </li>
[{/foreach}]
