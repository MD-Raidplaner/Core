{capture assign='pageTitle'}
    {$__wcf->getActivePage()->getTitle()}
    {if $listView->getPageNo() > 1} - {lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {$__wcf->getActivePage()->getTitle()}
    <span class="badge">{#$listView->countItems()}</span>
{/capture}

{capture assign='headContent'}
    {if $listView->getPageNo() < $listView->countPages()}
        <link rel="next" href="{link application='rp' controller='CharacterList'}pageNo={$listView->getPageNo() + 1}{/link}">
    {/if}
    {if $listView->getPageNo() > 1}
        <link rel="prev"
            href="{link application='rp' controller='CharacterList'}{if $listView->getPageNo() > 2}pageNo={$listView->getPageNo() - 1}{/if}{/link}">
    {/if}
{/capture}

{capture assign='contentHeaderNavigation'}
    {if $__wcf->getSession()->getPermission('user.rp.canAddCharacter')}
        <li>
            <a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button buttonPrimary">
                {icon name="plus"}
                <span>{lang}rp.character.add{/lang}</span>
            </a>
        </li>
    {/if}
{/capture}

{include file='header'}

<div class="section">
    {unsafe:$listView->render()}
</div>

{include file='footer'}