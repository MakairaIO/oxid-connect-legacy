<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--header">Kategorien</li>

[{foreach from=$categories item=category}]
    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--category">
        <a href="[{$category.link}]" class="makaira-autosuggestion__link">
            <span class="makaira-autosuggestion__title">[{$category.label}]</span>
        </a>
    </li>
[{/foreach}]
