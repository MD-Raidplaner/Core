{hascontent}
<div class="characterCard__buttons">
    {content}
    {if $character->userID == $__wcf->user->userID}
        {if $__wcf->session->getPermission('user.rp.canEditOwnCharacter')}
            <li>
                <a class="characterCard__button jsTooltip"
                    href="{link application='rp' controller='CharacterEdit' id=$character->getObjectID()}{/link}"
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