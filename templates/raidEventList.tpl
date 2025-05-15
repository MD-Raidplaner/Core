{capture assign='pageTitle'}
    {$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>
{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link application='rp' controller='RaidEventList'}pageNo={$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev"
            href="{link application='rp' controller='RaidEventList'}{if $pageNo > 2}pageNo={$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical"
        href="{link application='rp' controller='RaidEventList'}{if $pageNo > 1}pageNo={$pageNo}{/if}{/link}">
{/capture}

{capture assign='contentInteractionPagination'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='RaidEventList'}{/link}">
    </woltlab-core-pagination>
{/capture}

{include file='header'}

{if $objects|count}
    <div class="section sectionContainerList">
        <ol class="containerList pointList doubleColumned">
            {foreach from=$objects item=event}
                <li class="box64">
                    {unsafe:$event->getIcon(64)}

                    <div>
                        <div class="containerHeadline">
                            <h3>
                                <a href="{$event->getLink()}">{$event->getTitle()}</a>
                            </h3>
                        </div>

                        <dl class="plain dataList containerContent small">
                            <dt>{lang}rp.raid.event.pointAccount{/lang}</dt>
                            <dd>{if $event->getPointAccount()}{$event->getPointAccount()->getTitle()}{else}-{/if}</dd>
                        </dl>
                    </div>
                </li>
            {/foreach}
        </ol>
    </div>

    <footer class="contentFooter">
        {hascontent}
        <div class="paginationBottom">
            {content}
            <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
                url="{link application='rp' controller='RaidEventList'}{/link}">
            </woltlab-core-pagination>
            {/content}
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