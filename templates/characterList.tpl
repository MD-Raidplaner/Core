{capture assign='pageTitle'}
    {if $searchID}
        {lang}rp.character.search.results{/lang}
    {else}
        {$__wcf->getActivePage()->getTitle()}
    {/if}
    {if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}
{/capture}
{capture assign='contentTitle'}
    {if $searchID}
        {lang}rp.character.search.results{/lang}
    {else}
        {$__wcf->getActivePage()->getTitle()}
    {/if}
    <span class="badge">{#$items}</span>
{/capture}
{capture assign='canonicalURLParameters'}
    sortField={$sortField}&sortOrder={$sortOrder}{if $letter}&letter={$letter|rawurlencode}{/if}
{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next"
            href="{link application='rp' controller='CharacterList'}pageNo={$pageNo+1}&{$canonicalURLParameters}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev"
            href="{link application='rp' controller='CharacterList'}{if $pageNo > 2}pageNo={$pageNo-1}&{/if}{$canonicalURLParameters}{/link}">
    {/if}
    <link rel="canonical"
        href="{link application='rp' controller='CharacterList'}{if $pageNo > 1}pageNo={$pageNo}&{/if}{$canonicalURLParameters}{/link}">
{/capture}

{capture assign='contentHeaderNavigation'}
    {if $__wcf->getSession()->getPermission('user.rp.canAddCharacter')}
        <li>
            <a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
                {icon name="plus"}
                <span>{lang}rp.character.add{/lang}</span>
            </a>
        </li>
    {/if}
{/capture}

{capture assign='sidebarRight'}
    {assign var=encodedLetter value=$letter|rawurlencode}
    <section class="jsOnly box">
        <form method="post" action="{link application='rp' controller='CharacterSearch'}{/link}">
            <h2 class="boxTitle">
                <a href="{link application='rp' controller='CharacterSearch'}{/link}">
                    {lang}rp.character.search{/lang}
                </a>
            </h2>

            <div class="boxContent">
                <dl>
                    <dt></dt>
                    <dd>
                        <input type="text" id="searchCharacterName" name="characterName" class="long"
                            placeholder="{lang}rp.character.characterName{/lang}">
                        {csrfToken}
                    </dd>
                </dl>
            </div>
        </form>
    </section>
{/capture}

{capture assign='contentInteractionPagination'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='CharacterList' id=$searchID}pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&letter=$encodedLetter{/link}">
    </woltlab-core-pagination>
{/capture}

{include file='header'}

{if $items}
    <div class="section sectionContainerList">
        <div class="containerListDisplayOptions">
            <div class="containerListSortOptions">
                <a rel="nofollow" class="jsTooltip"
                    href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField={$sortField}&sortOrder={if $sortOrder == 'ASC'}DESC{else}ASC{/if}{if $letter}&letter={$letter}{/if}{if $ownCharacters}&ownCharacters=1{/if}{/link}"
                    title="{lang}wcf.global.sorting{/lang} ({lang}wcf.global.sortOrder.{if $sortOrder === 'ASC'}ascending{else}descending{/if}{/lang})">
                    {if $sortOrder === 'ASC'}
                        {icon name='arrow-down-short-wide'}
                    {else}
                        {icon name='arrow-down-wide-short'}
                    {/if}
                </a>

                <span class="dropdown">
                    <span class="dropdownToggle">{lang}rp.character.sortField.{$sortField}{/lang}</span>

                    <ul class="dropdownMenu">
                        {foreach from=$validSortFields item=_sortField}
                            <li {if $_sortField === $sortField}class="active" {/if}>
                                <a rel="nofollow"
                                    href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField={$_sortField}&sortOrder={if $sortField === $_sortField}{if $sortOrder === 'DESC'}ASC{else}DESC{/if}{else}{$sortOrder}{/if}{if $letter}&letter={$letter}{/if}{if $ownCharacters}&ownCharacters=1{/if}{/link}">
                                    {lang}rp.character.sortField.{$_sortField}{/lang}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                </span>
            </div>

            {hascontent}
            <div class="containerListActiveFilters">
                <ul class="inlineList">
                    {content}
                    {if $letter}
                        <li class="jsTooltip" title="{lang}rp.character.sort.letters{/lang}">
                            {icon name='bold'}
                            {$letter}
                        </li>
                    {/if}
                    {if $ownCharacters}
                        <li class="jsTooltip" title="{lang}rp.character.sort.ownCharacters{/lang}">
                            {icon name='user'}
                        </li>
                    {/if}
                    {/content}
                </ul>
            </div>
            {/hascontent}

            <div class="containerListFilterOptions jsOnly">
                <button type="button" class="button small jsStaticDialog" data-dialog-id="charactersListSortFilter">
                    {icon name='filter'}
                    {lang}wcf.global.filter{/lang}
                </button>
            </div>
        </div>

        <ol class="containerList characterList">
            {foreach from=$objects item=character}
                {include application='rp' file='characterListItem'}
            {/foreach}
        </ol>
    </div>

    <div id="charactersListSortFilter" class="jsStaticDialogContent" data-title="{lang}rp.character.filter{/lang}">
        <form method="post" action="{link application='rp' controller='CharacterList' id=$searchID}{/link}">
            <div class="section">
                <dl>
                    <dt><label for="letter">{lang}rp.character.sort.letters{/lang}</label></dt>
                    <dd>
                        <select name="letter" id="letter">
                            <option value="">{lang}rp.character.sort.letters.all{/lang}</option>
                            {foreach from=$letters item=__letter}
                                <option value="{$__letter}" {if $__letter == $letter} selected{/if}>{$__letter}</option>
                            {/foreach}
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt></dt>
                    <dd>
                        <label>
                            <input name="ownCharacters" type="checkbox" value="1" {if $ownCharacters} checked{/if}>
                            {lang}rp.character.sort.ownCharacters{/lang}
                        </label>
                    </dd>
                </dl>
            </div>

            <div class="formSubmit">
                <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
                <a href="{link application='rp' controller='CharacterList'}{/link}" class="button">
                    {lang}wcf.global.button.reset{/lang}
                </a>
                <input type="hidden" name="sortField" value="{$sortField}">
                <input type="hidden" name="sortOrder" value="{$sortOrder}">
            </div>
        </form>
    </div>
{else}
    <woltlab-core-notice type="info">{lang}rp.character.noCharacters{/lang}</woltlab-core-notice>
{/if}

<footer class="contentFooter">
    {hascontent}
    <div class="paginationBottom">
        {content}{unsafe:$contentInteractionPagination}{/content}
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

<script data-relocate="true">
    require(['MDRP/Ui/Character/Search/Input'], ({ UiCharacterSearchInput }) => {
        new UiCharacterSearchInput(document.getElementById('searchCharacterName'), {
            callbackSelect(item) {
                const link = '{link controller='Character' application='rp' id=2147483646 title='wcftitleplaceholder' encode=false}{/link}';
                window.location = link.replace('2147483646', item.dataset.objectId).replace(
                    'wcftitleplaceholder',
                    item.dataset.label);
            }
        });
    });
</script>

{include file='footer'}