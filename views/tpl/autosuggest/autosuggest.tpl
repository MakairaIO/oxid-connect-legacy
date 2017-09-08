<ul class="makaira-autosuggestion__list">

    [{* categories *}]
    [{if $result.categories}]
        [{include file="makaira/autosuggest/types/categories.tpl" categories=$result.categories }]
    [{/if}]

    [{* links *}]
    [{if $result.links}]
        [{include file="makaira/autosuggest/types/links.tpl" links=$result.links }]
    [{/if}]

    [{* manufacturers *}]
    [{if $result.manufacturers}]
        [{include file="makaira/autosuggest/types/manufacturers.tpl" manufacturers=$result.manufacturers }]
    [{/if}]

    [{* products *}]
    [{include file="makaira/autosuggest/types/products.tpl" products=$result.products }]

    <li class="makaira-autosuggestion__list-item makaira-autosuggestion__list-item--show-all">
        <button class="makaira-autosuggestion__submit" type="submit">
            Alle Ergebnisse anzeigen ([{$result.productCount}])
        </button>
    </li>

</ul>
    
