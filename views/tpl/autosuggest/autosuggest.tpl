<ul class="makaira-autosuggestion__list">
    [{foreach from=$result.items item=item}]
        <li class="makaira-autosuggestion__list-item">
            <a href="[{$item.link}]" class="makaira-autosuggestion__link">
                <figure class="makaira-autosuggestion__image"j>
                    <img src="[{$item.image}]">
                </figure>
                <span class="makaira-autosuggestion__title">[{$item.label}]</span>
            </a>
        </li>
    [{/foreach}]

    <li class="makaira-autosuggestion__list-item">
        <button class="makaira-autosuggestion__submit" type="submit">
            Alle Ergebnisse anzeigen ([{$result.productCount}])
        </button>
    </li>
</ul>
    
