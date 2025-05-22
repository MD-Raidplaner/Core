<div class="characterCard">
    <div class="characterCard__header">
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

        {hascontent}
        <div class="characterCard__buttons">
            {content}
            {if $character->userID == $__wcf->user->userID}
                {if $__wcf->session->getPermission('user.rp.canEditOwnCharacter')}
                    <li>
                        <a class="characterCard__button jsTooltip"
                            href="{link application='rp' controller='CharacterEdit' id=$character->characterID}{/link}"
                            title="{lang}rp.character.edit{/lang}">
                            {icon name='pencil' size='24' type='solid'}
                        </a>
                    </li>
                {/if}
                {if $character->canDelete()}
                    <button type="button" class="characterCard__button jsTooltip"
                        data-confirm-message="{lang objectTitle=$character->getTitle() __encode=true}wcf.button.delete.confirmMessage{/lang}"
                        title="{lang}wcf.global.button.delete{/lang}">
                        {icon name='times' size='24' type='solid'}
                    </button>
                {/if}
            {/if}

            {event name='buttons'}
            {/content}
        </div>
        {/hascontent}

        {event name='afterButtons'}

        {hascontent}
        <div class="characterCard__details">
            <dl class="plain dataList">
                {content}
                {event name='beforeDetails'}

                {if !$character->guildName|empty}
                    <dt>{lang}rp.character.guildName{/lang}</dt>
                    <dd>{$character->guildName}</dd>
                {/if}

                <dt>{lang}rp.character.created{/lang}</dt>
                <dd>{unsafe:$character->created|date}</dd>

                {event name='afterDetails'}
                {/content}
            </dl>
        </div>
        {/hascontent}
    </div>

    {hascontent}
    <div class="characterCard__footer">
        <div class="characterCard__footer__stats">
            {content}
            {event name='beforeStats'}



            {event name='afterStats'}
            {/content}
        </div>
    </div>
    {/hascontent}
</div>