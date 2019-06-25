[{if $category_result || $manufacturer_result || $links_result || $suggestion_result}]
    [{if $suggestion_result}]
        <div class="makaira-search__results">
            <p class="makaira-search__result-header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_SUGGESTION" alternative=""}]</p>
            <ul class="makaira-search__result-list">
                [{foreach from=$suggestion_result->items item="suggestion"}]
                    <li class="makaira-search__result-item">
                        <a href="[{$oViewConf->getSelfActionLink()|cat:"cl=search&searchparam="|cat:$suggestion->fields.title}]">
                            [{$suggestion->fields.title}]
                        </a>
                    </li>
                [{/foreach}]
            </ul>
        </div>
    [{/if}]
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
    [{if $links_result}]
        <div class="makaira-search__results">
            <p class="makaira-search__result-header">[{oxmultilang ident="MAKAIRA_SEARCHRESULT_SEARCHLINK" alternative=""}]</p>
            <ul class="makaira-search__result-list">
                [{foreach from=$links_result->items item="link"}]
                    <li class="makaira-search__result-item">
                        <a href="[{$link->fields.url}]" [{if $link->fields.isExternal}]target="_blank" rel="noreferrer noopener"[{/if}]>
                            [{$link->fields.title}]
                        </a>
                    </li>
                [{/foreach}]
            </ul>
        </div>
    [{/if}]
[{/if}]
