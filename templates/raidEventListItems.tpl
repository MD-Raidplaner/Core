<div class="contentItemList">
    {foreach from=$view->getItems() item=event}
        <div class="contentItem contentItemMultiColumn listView__item" data-object-id="{$event->getObjectID()}">
            <div class="contentItemOptions">
                {if $view->hasBulkInteractions()}
                    <label class="button small jsTooltip" title="{lang}wcf.clipboard.item.mark{/lang}">
                        <input type="checkbox" class="listView__selectItem" aria-label="{lang}wcf.clipboard.item.mark{/lang}">
                    </label>
                {/if}

                {unsafe:$view->renderInteractionContextMenuButton($event)}
            </div>

            <div class="contentItemLink">
                <div class="contentItemImage">
                    {unsafe:$event->getIcon(64)}
                </div>
            </div>

            <div class="contentItemContent">
                <h2 class="contentItemTitle">
                    <a href="{$event->getLink()}" class="contentItemTitleLink">
                        {$event->getTitle()}
                    </a>
                </h2>

                <div class="contentItemDescription">
                    <dl class="plain dataList containerContent small">
                        <dt>{lang}rp.raid.event.pointAccount{/lang}</dt>
                        <dd>{if $event->getPointAccount()}{$event->getPointAccount()->getTitle()}{else}-{/if}</dd>
                    </dl>
                </div>
            </div>
        </div>
    {/foreach}
</div>