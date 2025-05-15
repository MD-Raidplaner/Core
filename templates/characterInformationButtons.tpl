{hascontent}
<nav class="jsMobileNavigation buttonGroupNavigation">
    <ul class="buttonList iconList jsObjectActionContainer"
        data-object-action-class-name="rp\data\character\CharacterAction">
        {content}
        {if $character->userID == $__wcf->user->userID}
            {if $__wcf->session->getPermission('user.rp.canEditOwnCharacter')}
                <li>
                    <a href="{link application='rp' controller='CharacterEdit' id=$character->characterID}{/link}"
                        class="jsTooltip" title="{lang}rp.character.edit{/lang}">
                        {icon name='pencil'}
                        <span class="invisible">{lang}rp.character.edit{/lang}</span>
                    </a>
                </li>
            {/if}
            {if $character->canDelete()}
                <li class="jsObjectActionObject" data-object-id="{$character->getObjectID()}">
                    <a class="jsObjectAction pointer" data-object-action="deleteOwnCharacter"
                        data-confirm-message="{lang objectTitle=$character->getTitle() __encode=true}wcf.button.delete.confirmMessage{/lang}"
                        data-object-action-success="reload" data-tooltip="{lang}wcf.global.button.delete{/lang}"
                        aria-label="{lang}wcf.global.button.delete{/lang}">
                        {icon name='times'}
                    </a>
                </li>
            {/if}
        {/if}

        {event name='buttons'}
        {/content}
    </ul>
</nav>
{/hascontent}