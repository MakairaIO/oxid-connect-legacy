[{if $category_result || $manufacturer_result || $searchlink_result}]
    <div id="additional_searchresults">
        [{if $category_result}]
            <div class="searchresults" id="category">
                [{oxmultilang ident="MAKAIRA_SEARCHRESULT_CATEGORY" alternative=""}]
                <ul>
                    [{foreach from=$category_result->items item="category"}]
                        <li><a href="[{oxgetseourl oxid=$category->id type="oxCategory"}]">[{$category->fields.oxtitle}]</a></li>
                    [{/foreach}]
                </ul>
            </div>
        [{/if}]
        [{if $manufacturer_result}]
            [{if $category_result}]<div class="searchresults separator"></div>[{/if}]
            <div class="searchresults" id="manufacturer">
                [{oxmultilang ident="MAKAIRA_SEARCHRESULT_MANUFACTURER" alternative=""}]
                <ul>
                    [{foreach from=$manufacturer_result->items item="manufacturer"}]
                        <li><a href="[{oxgetseourl oxid=$manufacturer->id type="oxManufacturer"}]">[{$manufacturer->fields.oxtitle}]</a></li>
                    [{/foreach}]
                </ul>
            </div>
        [{/if}]
        [{if $searchlink_result}]
            [{if $category_result || $manufacturer_result}]<div class="searchresults separator"></div>[{/if}]
            <div class="searchresults" id="searchlink">
                [{oxmultilang ident="MAKAIRA_SEARCHRESULT_SEARCHLINK" alternative=""}]
                <ul>
                    [{foreach from=$searchlink_result item="searchlink"}]
                        <li>
                            <a href="[{$searchlink->fields.link}]" [{if $searchlink->fields.external}]target="_blank" rel="noreferrer noopener"[{/if}]>
                                [{$searchlink->fields.title}]
                            </a>
                        </li>
                    [{/foreach}]
                </ul>
            </div>
        [{/if}]
        <div class="clear"></div>
    </div>
[{/if}]
