{capture assign='pageTitle'}
    {$__wcf->getActivePage()->getTitle()}
    {if $raidEvent} ({$raidEvent->getTitle()}){/if}
    {if $listView->getPageNo() > 1} - {lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {$__wcf->getActivePage()->getTitle()}
    {if $raidEvent} ({$raidEvent->getTitle()}){/if}
    <span class="badge">{#$listView->countItems()}</span>
{/capture}
{if $raidEvent}
    {capture assign='contentDescription'}
        {lang}rp.raid.event.pointAccount{/lang}:
        {if $raidEvent->getPointAccount()}{$raidEvent->getPointAccount()->getTitle()}{else}-{/if}
    {/capture}
{/if}

{capture assign='headContent'}
    {if $listView->getPageNo() < $listView->countPages()}
        <link rel="next"
            href="{link application='rp' controller='RaidList'}pageNo={$listView->getPageNo() + 1}{if $raidEvent}&raidEventID={$raidEvent->getObjectID()}{/if}{/link}">
    {/if}
    {if $listView->getPageNo() > 1}
        <link rel="prev"
            href="{link application='rp' controller='RaidList'}{if $listView->getPageNo() > 2}pageNo={$listView->getPageNo() - 1}{/if}{if $raidEvent}&raidEventID={$raidEvent->getObjectID()}{/if}{/link}">
    {/if}
{/capture}

{capture assign='contentHeaderNavigation'}
    {if $__wcf->getSession()->getPermission('mod.rp.canAddRaid')}
        <li>
            <a href="{link application='rp' controller='RaidAdd'}{/link}" class="button">
                {icon name='plus'}
                <span>{lang}rp.raid.add{/lang}</span>
            </a>
        </li>
    {/if}
{/capture}

{include file='header'}

<div class="section">
    {unsafe:$listView->render()}
</div>

{include file='footer'}