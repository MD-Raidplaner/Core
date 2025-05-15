{include file='header' pageTitle='rp.acp.raid.event.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}rp.acp.raid.event.list{/lang}
            {if $items}
                <span class="badge badgeInverse">{#$items}</span>
            {/if}
        </h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link controller='RaidEventAdd' application='rp'}{/link}" class="button">
                    {icon name='plus'}
                    <span>{lang}rp.acp.raid.event.add{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{capture assign='pagesLinks'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='RaidEventList'}pageNo=%d{/link}">
    </woltlab-core-pagination>
{/capture}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="rp\data\raid\event\RaidEventAction">
            <thead>
                <tr>
                    <th class="columnID columnEventID" colspan="2">
                        <a href="{link application='rp' controller='RaidEventList'}pageNo={$pageNo}{/link}">
                            {lang}wcf.global.objectID{/lang}
                        </a>
                    </th>
                    <th class="columnText columnEventName">
                        <a href="{link application='rp' controller='RaidEventList'}pageNo={$pageNo}{/link}">
                            {lang}wcf.global.name{/lang}
                        </a>
                    </th>
                    <th class="columnText columnPointAccountName">
                        <a href="{link application='rp' controller='RaidEventList'}pageNo={$pageNo}{/link}">
                            {lang}rp.acp.raid.event.point.account{/lang}
                        </a>
                    </th>
                    <th class="columnDate columnDefaultPoints">
                        <a href="{link application='rp' controller='RaidEventList'}pageNo={$pageNo}{/link}">
                            {lang}rp.acp.raid.event.defaultPoints{/lang}
                        </a>
                    </th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=event}
                    <tr class="jsEventRow jsObjectActionObject" data-object-id="{$event->eventID}">
                        <td class="columnIcon">
                            <a href="{link application='rp' controller='RaidEventEdit' id=$event->eventID}{/link}"
                                title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
                                {icon name='pencil'}
                            </a>
                            {objectAction action="delete" objectTitle=$event->getTitle()}
                        </td>
                        <td class="columnID columnEventID">{$event->eventID}</td>
                        <td class="columnText columnEventName">
                            <a href="{link application='rp' controller='RaidEventEdit' id=$event->eventID}{/link}">
                                {$event->getTitle()}
                            </a>
                        </td>
                        <td class="columnText columnPointAccountName">{$event->pointAccountName}</td>
                        <td class="columnDigits columnPoints">{#$event->defaultPoints}</td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        <div class="paginationBottom">
            {$pagesLinks}
        </div>

        <nav class="contentFooterNavigation">
            <ul>
                <li>
                    <a href="{link controller='RaidEventAdd' application='rp'}{/link}" class="button">
                        {icon name='plus'}
                        <span>{lang}rp.acp.raid.event.add{/lang}</span>
                    </a>
                </li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}