{if !$characterCardDetails|empty}
    {unsafe:$characterCardDetails}
{else}
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
{/if}