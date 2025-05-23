<ol class="characterCardList">
    {foreach from=$view->getItems() item='character'}
        <div class="characterCard listView__item" data-object-id="{$character->getObjectID()}">
            <div class="characterCard__header">
                <div class="contentItemOptions">
                    {if $view->hasBulkInteractions()}
                        <label class="button small jsTooltip" title="{lang}wcf.clipboard.item.mark{/lang}">
                            <input type="checkbox" class="listView__selectItem"
                                aria-label="{lang}wcf.clipboard.item.mark{/lang}">
                        </label>
                    {/if}

                    {unsafe:$view->renderInteractionContextMenuButton($character)}
                </div>

                <div class="characterCard__header__avatar">
                    {character object=$character type='avatar64' ariaHidden='true' tabindex='-1'}
                </div>
            </div>

            <div class="characterCard__content">
                <h3 class="characterCard__name">
                    <a href="{$character->getLink()}">{unsafe:$character->getTitle()}</a>

                    {if $character->isPrimary}
                        <span class="badge green characterPrimary">
                            {lang}rp.character.primary{/lang}
                        </span>
                    {/if}
                </h3>

                {event name='afterCharacterName'}

                {include application='rp' file='characterCardDetails'}
            </div>

            {include application='rp' file='characterCardFooter'}
        </div>
    {/foreach}
</ol>