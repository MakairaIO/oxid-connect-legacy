[{if $category_result || $manufacturer_result || $searchlink_result}]
    [{if $category_result}]
        <div class="makaira-search__results">
            <p class="makaira-search__result-header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_CATEGORY" alternative=""}]</p>
            <ul class="makaira-search__result-list">
                [{foreach from=$category_result->items item="category"}]
                    <li class="makaira-search__result-item">
                        <a href="[{oxgetseourl oxid=$category->id type="oxCategory"}]">
                            [{$category->fields.oxtitle}]
                        </a>
                    </li>
                [{/foreach}]
            </ul>
        </div>
    [{/if}]
    [{if $manufacturer_result}]
        <div class="makaira-search__results">
            <p class="makaira-search__result-header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_MANUFACTURER" alternative=""}]</p>
            <ul class="makaira-search__result-list">
                [{foreach from=$manufacturer_result->items item="manufacturer"}]
                    <li class="makaira-search__result-item">
                        <a href="[{oxgetseourl oxid=$manufacturer->id type="oxManufacturer"}]">
                            [{$manufacturer->fields.oxtitle}]
                        </a>
                    </li>
                [{/foreach}]
            </ul>
        </div>
    [{/if}]
    [{if $searchlink_result}]
        <div class="makaira-search__results">
            <p class="makaira-search__result-header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_SEARCHLINK" alternative=""}]</p>
            <ul class="makaira-search__result-list">
                [{foreach from=$searchlink_result item="searchlink"}]
                    <li class="makaira-search__result-item">
                        <a href="[{$searchlink->fields.link}]" [{if $searchlink->fields.external}]target="_blank" rel="noreferrer noopener"[{/if}]>
                            [{$searchlink->fields.title}]
                        </a>
                    </li>
                [{/foreach}]
            </ul>
        </div>
    [{/if}]
[{/if}]
