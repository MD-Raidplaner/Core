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

        {if !$disableCharacterCardButtons|isset || $disableCharacterCardButtons != true}
            {include application='rp' file='characterCardButtons'}
        {/if}

        {event name='afterButtons'}

        {include application='rp' file='characterCardDetails'}
    </div>

    {include application='rp' file='characterCardFooter'}
</div>