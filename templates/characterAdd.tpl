{if $action == 'edit'}
    {capture assign='contentDescription'}{$formObject->getTitle()}{/capture}
{/if}

{capture assign='contentHeaderNavigation'}
    {if $action == 'edit' && !$formObject->isPrimary}
        <li>
            <button class="button">
                {icon name="refresh"}
                <span>{lang}rp.character.setAsMain{/lang}</span>
            </button>
        </li>
    {/if}

    <li>
        <a href="{link application='rp' controller='CharacterList'}{/link}" class="button">
            {icon name="list"}
            <span>{lang}rp.character.list{/lang}</span>
        </a>
    </li>
{/capture}

{include file='header'}

{unsafe:$form->getHtml()}

{include file='footer'}