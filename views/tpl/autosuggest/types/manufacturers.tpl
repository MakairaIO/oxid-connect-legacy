<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_MANUFACTURER"}]</li>

[{foreach from=$manufacturers item=manufacturer}]
    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--manufacturer">
        <a href="[{$manufacturer.link}]" class="makaira-autosuggestion__link">
            <figure class="makaira-autosuggestion__image"j>
                <img src="[{$manufacturer.image}]">
            </figure>
            <span class="makaira-autosuggestion__title">[{$manufacturer.label}]</span>
        </a>
    </li>
[{/foreach}]
