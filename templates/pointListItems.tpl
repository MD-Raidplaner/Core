<ol class="characterCardList">
    {foreach from=$view->getItems() item='character'}
        <div class="characterCard listView__item" data-object-id="{$character->getObjectID()}">
            <div class="characterCard__header">
                <div class="characterCard__header__avatar">
                    {character object=$character type='avatar64' ariaHidden='true' tabindex='-1'}
                </div>
            </div>

            <div class="characterCard__content">
                <h3 class="characterCard__name">
                    <a href="{$character->getLink()}">{unsafe:$character->getTitle()}</a>
                </h3>

                {event name='afterCharacterName'}

                {assign var='__characterPoints' value=$__rp->getCharacterPointHandler()->getPoints($character)}
                {capture assign='characterCardDetails'}
                    <div class="characterCard__details">
                        <dl class="plain dataList">
                            {foreach from=$pointAccounts item=pointAccount}
                                {assign var='__accountID' value=$pointAccount->getObjectID()}
                                {if $__characterPoints[$__accountID]|isset}
                                    {include application="rp" file="pointDetails" pointsData=$__characterPoints[$__accountID] key="current" langKey=$pointAccount->getTitle()}
                                {/if}
                            {/foreach}
                        </dl>
                    </div>
                {/capture}

                {include application='rp' file='characterCardDetails'}
            </div>
        </div>
    {/foreach}
</ol>