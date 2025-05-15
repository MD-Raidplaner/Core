{include file='header' pageTitle='rp.acp.character.search'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.character.search{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
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

                    {event name='quickSearchItems'}
                </ul>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{if $errorField == 'search'}
	<woltlab-core-notice type="error">{lang}rp.character.search.error.noMatches{/lang}</woltlab-core-notice>
{else}
	{include file='shared_formError'}
{/if}

{unsafe:$form->getHtml()}

{include file='footer'}