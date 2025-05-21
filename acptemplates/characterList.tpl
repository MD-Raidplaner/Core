{if $searchID}
    {assign var='pageTitle' value='rp.acp.character.search'}
{else}
    {assign var='pageTitle' value='rp.acp.character.list'}
{/if}

{include file='header'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}{$pageTitle}{/lang}
            <span class="badge badgeInverse">{#$gridView->countRows()}</span>
        </h1>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->session->getPermission('admin.rp.canSearchCharacter')}
                <li class="dropdown">
                    <a class="button dropdownToggle">
                        {icon name='magnifying-glass'}
                        <span>{lang}rp.acp.character.quickSearch{/lang}</span>
                    </a>
                    <ul class="dropdownMenu">
                        <li>
                            <a href="{link application='rp' controller='CharacterQuickSearch'}mode=disabled{/link}">
                                {lang}rp.acp.character.quickSearch.disabled{/lang}
                            </a>
                        </li>
                    </ul>
                </li>
            {/if}

            {if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
                <li>
                    <a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
                        {icon name='plus'}
                        <span>{lang}rp.character.add{/lang}</span>
                    </a>
                </li>
            {/if}

            {event name='contentHeaderNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}