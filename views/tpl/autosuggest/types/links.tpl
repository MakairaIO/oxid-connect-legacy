<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--header">Links</li>

[{foreach from=$links item=link}]
    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--link">
        <a href="[{$link.link}]" class="makaira-autosuggestion__link">
            <span class="makaira-autosuggestion__title">[{$link.label}]</span>
        </a>
    </li>
[{/foreach}]
