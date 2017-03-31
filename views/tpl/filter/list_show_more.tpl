<button type="button" class="makaira-filter__button--show-more">[{oxmultilang ident="MARM_OXSEARCH_FILTER_SHOW_MORE"}]</button>

[{capture}]
    <script>
    [{capture assign="expandScript"}]
        $('#[{$facetKey}] .toHide').hide();
        $('#[{$facetKey}] .filterShowMore').click(function() {
            $('#[{$facetKey}] .toHide').slideToggle(function() {
                $('#[{$facetKey}] .filterShowMore').text($('#[{$facetKey}] .toHide').is(':visible') ? "[{oxmultilang ident="MARM_OXSEARCH_FILTER_SHOW_LESS"}]" : "[{oxmultilang ident="MARM_OXSEARCH_FILTER_SHOW_MORE"}]");
            });
        });
    [{/capture}]
    </script>
[{/capture}]
[{oxscript add=$expandScript}]
