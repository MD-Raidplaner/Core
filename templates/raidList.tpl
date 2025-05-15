{capture assign='pageTitle'}
    {$__wcf->getActivePage()->getTitle()}{if $raidEvent} ({$raidEvent->getTitle()}){/if}{if $pageNo > 1} -
{lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {$__wcf->getActivePage()->getTitle()}{if $raidEvent} ({$raidEvent->getTitle()}){/if} <span
        class="badge">{#$items}</span>
{/capture}
{if $raidEvent}
    {capture assign='contentDescription'}
        {lang}rp.raid.event.pointAccount{/lang}:
        {if $raidEvent->getPointAccount()}{$raidEvent->getPointAccount()->getTitle()}{else}-{/if}
    {/capture}
{/if}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link application='rp' controller='RaidList'}pageNo={$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link application='rp' controller='RaidList'}{if $pageNo > 2}pageNo={$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical" href="{link application='rp' controller='RaidList'}{if $pageNo > 1}pageNo={$pageNo}{/if}{/link}">
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

{capture assign='contentInteractionPagination'}
    {pages print=true assign=pagesLinks controller='RaidList' application='rp' link="pageNo=%d"}
{/capture}

{include file='header'}

{if $objects|count}
    <div class="section sectionContainerList">
        <ol class="containerList raidList doubleColumned">
            {foreach from=$objects item=raid}
                <li class="box64">
                    <div>{unsafe:$raid->getIcon(64)}</div>

                    <div>
                        <div class="containerHeadline">
                            <h3><a href="{$raid->getLink()}">{$raid->getTitle()}</a></h3>
                        </div>

                        <dl class="plain dataList containerContent small">
                            <dt>{lang}rp.raid.time{/lang}</dt>
                            <dd>{$raid->time|date}</dd>

                            <dt>{lang}rp.raid.attendees{/lang}</dt>
                            <dd>{$raid->getAttendees()|count}</dd>

                            <dt>{lang}rp.raid.points{/lang}</dt>
                            <dd>{#$raid->points}</dd>

                            <dt>{lang}rp.raid.notes{/lang}</dt>
                            <dd class="tooltip" title="{$raid->notes}">
                                {if $raid->notes|empty}-{else}{$raid->notes|truncate:100}{/if}
                            </dd>
                        </dl>
                    </div>
                </li>
            {/foreach}
        </ol>
    </div>

    <footer class="contentFooter">
        {hascontent}
        <div class="paginationBottom">
            {content}{@$pagesLinks}{/content}
        </div>
        {/hascontent}

        {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
        {/hascontent}
    </footer>
{else}
    <woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
{/if}


{include file='footer'}