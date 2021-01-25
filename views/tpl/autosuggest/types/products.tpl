<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--header">[{oxmultilang ident="MAKAIRA_PRODUCTS"}]</li>

[{foreach from=$products item=product}]
    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--product">
        <a href="[{$product.link}]" class="makaira-autosuggestion__link">
            <figure class="makaira-autosuggestion__image"j>
                <img src="[{$product.image}]">
            </figure>
            <span class="makaira-autosuggestion__title">[{$product.label}]</span>
        </a>
    </li>
[{/foreach}]

<li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--show-all">
    <button class="makaira-autosuggestion__submit" type="submit">
        [{oxmultilang ident="MAKAIRA_SUGGESTION_SHOW_ALL"}] ([{$productCount}])
    </button>
</li>
