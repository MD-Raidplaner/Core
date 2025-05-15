{include file='header' pageTitle='rp.character.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.character.{$action}{/lang}</h1>
        {if $action == 'edit'}<p class="contentHeaderDescription">{$formObject->getTitle()}</p>{/if}
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='rp' controller='CharacterList'}{/link}" class="button">
                    {icon name='list'}
                    <span>{lang}rp.character.list{/lang}</span>
                </a>
            </li>
        </ul>
    </nav>
</header>

{unsafe:$form->getHtml()}

{include file='footer'}