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
        <link rel="next" href="{link application='rp' controller='RaidEventList'}pageNo={$listView->getPageNo() + 1}{/link}">
    {/if}
    {if $listView->getPageNo() > 1}
        <link rel="prev"
            href="{link application='rp' controller='RaidEventList'}{if $listView->getPageNo() > 2}pageNo={$listView->getPageNo() + 1}{/if}{/link}">
    {/if}
    <link rel="canonical"
        href="{link application='rp' controller='RaidEventList'}{if $listView->getPageNo() > 1}pageNo={$listView->getPageNo() + 1}{/if}{/link}">
{/capture}

{include file='header'}

<div class="section">
    {unsafe:$listView->render()}
</div>

{include file='footer'}