{foreach from=$view->getItems() item=raid}
    <div class="contentItem contentItemMultiColumn listView__item" data-object-id="{$raid->getObjectID()}">
        <div class="contentItemOptions">
            {if $view->hasBulkInteractions()}
                <label class="button small jsTooltip" title="{lang}wcf.clipboard.item.mark{/lang}">
                    <input type="checkbox" class="listView__selectItem" aria-label="{lang}wcf.clipboard.item.mark{/lang}">
                </label>
            {/if}

            {unsafe:$view->renderInteractionContextMenuButton($raid)}
        </div>

        <div class="contentItemLink">
            <div class="contentItemImage">
                {unsafe:$raid->getIcon(64)}
            </div>
        </div>

        <div class="contentItemContent">
            <h2 class="contentItemTitle">
                <a href="{$raid->getLink()}" class="contentItemTitleLink">
                    {$raid->getTitle()}
                </a>
            </h2>

            <div class="contentItemDescription">
                <dl class="plain dataList containerContent small">
                    <dt>{lang}rp.raid.time{/lang}</dt>
                    <dd>{$raid->time|date}</dd>

                    <dt>{lang}rp.raid.attendees{/lang}</dt>
                    <dd>{$raid->getAttendees()|count}</dd>

                    <dt>{lang}rp.raid.points{/lang}</dt>
                    <dd>{#$raid->points}</dd>

                    <dt>{lang}rp.raid.notes{/lang}</dt>
                    <dd class="tooltip" title="{$raid->notes}">
                        {if $raid->notes|empty}-{else}{$raid->notes|truncate:100}{/if}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
{/foreach}