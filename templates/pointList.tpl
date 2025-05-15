{capture assign='pageTitle'}
    {$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>
{/capture}

{capture assign='canonicalURLParameters'}
    {if $letter}&letter={@$letter|rawurlencode}{/if}
{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link application='rp' controller='PointList'}pageNo={$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link application='rp' controller='PointList'}{if $pageNo > 2}pageNo={$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical" href="{link application='rp' controller='PointList'}{if $pageNo > 1}pageNo={$pageNo}{/if}{/link}">
{/capture}

{capture assign='sidebarRight'}
    {assign var=encodedLetter value=$letter|rawurlencode}
    <section class="jsOnly box">
        <h2 class="boxTitle">{lang}rp.character.sort.letters{/lang}</h2>

        <div class="boxContent">
            <ul class="buttonList smallButtons letters">
                {foreach from=$letters item=__letter}
                    <li>
                        <a href="{link application='rp' controller='PointList'}letter={$__letter|rawurlencode}{/link}"
                            class="button small{if $letter == $__letter} active{/if}">
                            {$__letter}
                        </a>
                    </li>
                {/foreach}
                {if !$letter|empty}
                    <li class="lettersReset">
                        <a href="{link application='rp' controller='PointList'}{/link}" class="button small">
                            {lang}rp.character.sort.letters.all{/lang}
                        </a>
                    </li>
                {/if}
            </ul>
        </div>
    </section>
{/capture}

{capture assign='contentInteractionPagination'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='PointList'}{$canonicalURLParameters}{/link}">
    </woltlab-core-pagination>
{/capture}

{include file='header'}

{if $objects|count}
    <div class="section sectionContainerList">
        <ol class="containerList pointList doubleColumned">
            {foreach from=$objects item=character}
                <li class="box48">
                    {character object=$character type='avatar48' ariaHidden='true' tabindex='-1'}

                    <div class="characterInformation">
                        {include application='rp' file='characterInformationHeadline'}

                        <dl class="plain dataList containerContent">
                            {assign var='__characterPoints' value=$__rp->getCharacterPointHandler()->getPoints($character)}
                            {foreach from=$pointAccounts item=pointAccount}
                                {assign var='__accountID' value=$pointAccount->getObjectID()}
                                {if $__characterPoints[$__accountID]|isset}
                                    {include application="rp" file="pointDetails" pointsData=$__characterPoints[$__accountID] key="current" langKey=$pointAccount->getTitle()}
                                {/if}
                            {/foreach}
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
                url="{link application='rp' controller='PointList'}{$canonicalURLParameters}{/link}">
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
    <woltlab-core-notice type="info">{lang}rp.point.account.noDatas{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}