{include file='header' pageTitle='rp.acp.point.account.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}rp.acp.point.account.list{/lang}
            {if $items}
                <span class="badge badgeInverse">{#$items}</span>
            {/if}
        </h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='rp' controller='PointAccountAdd'}{/link}" class="button">
                    {icon name='plus'}
                    <span>{lang}rp.acp.point.account.add{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{capture assign='pagesLinks'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='PointAccountList'}pageNo=%d{/link}">
    </woltlab-core-pagination>
{/capture}

<div class="paginationTop">
    {unsafe:$pagesLinks}
</div>

{if $objects|count}
    <div id="pointAccountTableContainer" class="section tabularBox">
        <table class="table jsObjectActionContainer"
            data-object-action-class-name="rp\data\point\account\PointAccountAction">
            <thead>
                <tr>
                    <th class="columnID columnAccountID" colspan="2">
                        {lang}wcf.global.objectID{/lang}
                    </th>
                    <th class="columnTitle columnName">
                        {lang}rp.acp.point.account.title{/lang}
                    </th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=account}
                    <tr class="jsAccountRow jsObjectActionObject" data-object-id="{$account->getObjectID()}">
                        <td class="columnIcon">
                            <a href="{link application='rp' controller='PointAccountEdit' id=$account->getObjectID()}{/link}"
                                title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
                                {icon name='pencil'}
                            </a>
                            {objectAction action="delete" objectTitle=$account->getTitle()}
                        </td>
                        <td class="columnID columnAccountID">{$account->getObjectID()}</td>
                        <td class="columnTitle columnName">
                            <a href="{link application='rp' controller='PointAccountEdit' id=$account->getObjectID()}{/link}">
                                {$account->getTitle()}
                            </a>
                        </td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        <div class="paginationBottom">
            {unsafe:$pagesLinks}
        </div>

        <nav class="contentFooterNavigation">
            <ul>
                <a href="{link application='rp' controller='PointAccountAdd'}{/link}" class="button">
                    {icon name='plus'}
                    <span>{lang}rp.acp.point.account.add{/lang}</span>
                </a>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}