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
    [{if $result.products}]
        [{include file="makaira/autosuggest/types/products.tpl" products=$result.products productCount=$result.productCount}]
    [{/if}]

</ul>
    
