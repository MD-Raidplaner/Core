{if $searchID}
    {assign var='pageTitle' value='rp.acp.character.search'}
{else}
    {assign var='pageTitle' value='rp.acp.character.list'}
{/if}

{include file='header'}

{event name='javascriptInclude'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}{$pageTitle}{/lang}
            {if $items}
                <span class="badge badgeInverse">{#$items}</span>
            {/if}
        </h1>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->session->getPermission('admin.rp.canSearchCharacter')}
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
                    </ul>
                </li>
            {/if}

            {if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
                <li>
                    <a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
                        {icon name='plus'}
                        <span>{lang}rp.character.add{/lang}</span>
                    </a>
                </li>
            {/if}

            {event name='contentHeaderNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</header>

{capture assign='pagesLinks'}
    <woltlab-core-pagination page="{$pageNo}" count="{$pages}"
        url="{link application='rp' controller='CharacterList' id=$searchID}pageNo=%d&sortField=$sortField&sortOrder=$sortOrder{/link}">
    </woltlab-core-pagination>
{/capture}

<div class="paginationTop">
    {unsafe:$pagesLinks}
</div>

{if $objects|count}
    <div id="characterTableContainer" class="section tabularBox">
        <table data-type="de.md-raidplaner.rp.character" class="table jsClipboardContainer jsObjectActionContainer"
            data-object-action-class-name="rp\data\character\CharacterAction">
            <thead>
                <tr>
                    <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll"></label></th>
                    <th class="columnID columnCharacterID{if $sortField == 'characterID'} active {$sortOrder}{/if}"
                        colspan="2">
                        <a
                            href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField=characterID&sortOrder={if $sortField == 'characterID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}wcf.global.objectID{/lang}
                        </a>
                    </th>
                    <th class="columnText columnCharacterName{if $sortField == 'characterName'} active {$sortOrder}{/if}"
                        colspan="2">
                        <a
                            href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField=characterName&sortOrder={if $sortField == 'characterName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}rp.character.characterName{/lang}
                        </a>
                    </th>
                    <th class="columnText columnUsername{if $sortField == 'username'} active {$sortOrder}{/if}">
                        <a
                            href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}rp.acp.character.owner{/lang}
                        </a>
                    </th>
                    <th class="columnDigits columnCreated{if $sortField == 'created'} active {$sortOrder}{/if}">
                        <a
                            href="{link application='rp' controller='CharacterList' id=$searchID}pageNo={$pageNo}&sortField=created&sortOrder={if $sortField == 'created' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}rp.character.created{/lang}
                        </a>
                    </th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=character}
                    <tr class="jsCharacterRow jsClipboardObject jsObjectActionObject"
                        data-object-id="{$character->getObjectID()}"
                        data-enabled="{if !$character->isDisabled}true{else}false{/if}">
                        <td class="columnMark">
                            <input type="checkbox" class="jsClipboardItem" data-object-id="{$character->getObjectID()}">
                        </td>
                        <td class="columnIcon">
                            <div class="dropdown" id="characterListDropdown{$character->getObjectID()}">
                                <a href="#" class="dropdownToggle button small">
                                    {icon name='pencil'}
                                    <span>{lang}wcf.global.button.edit{/lang}</span>
                                </a>

                                <ul class="dropdownMenu">
                                    {event name='dropdownItems'}

                                    {if $__wcf->session->getPermission('admin.rp.canEditCharacter')}
                                        <li>
                                            <a href="#" class="jsEnable" data-enable-message="{lang}rp.acp.character.enable{/lang}"
                                                data-disable-message="{lang}rp.acp.character.disable{/lang}">
                                                {lang}rp.acp.character.{if !$character->isDisabled}disable{else}enable{/if}{/lang}
                                            </a>
                                        </li>
                                    {/if}

                                    {if $__wcf->session->getPermission('admin.rp.canDeleteCharacter')}
                                        <li class="dropdownDivider"></li>
                                        <li>
                                            <a href="#" class="jsDelete" data-character-name="{$character->getTitle()}">
                                                {lang}wcf.global.button.delete{/lang}
                                            </a>
                                        </li>
                                    {/if}

                                    {if $__wcf->session->getPermission('admin.rp.canEditCharacter')}
                                        <li class="dropdownDivider"></li>
                                        <li>
                                            <a href="{link controller='CharacterEdit' application='rp' id=$character->getObjectID()}{/link}"
                                                class="jsEditLink">
                                                {lang}wcf.global.button.edit{/lang}
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <td class="columnID columnCharacterID">{$character->getObjectID()}</td>
                        <td class="columnIcon">{unsafe:$character->getAvatar()->getImageTag(24)}</td>
                        <td class="columnText columnCharacterName">
                            <span class="characterName">
                                <a href="{link application='rp' controller='CharacterEdit' id=$character->getObjectID()}{/link}">
                                    {$character->getTitle()}
                                </a>
                            </span>

                            {if !$character->isPrimary}
                                <span class="primaryCharacter">
                                    ({lang}rp.character.primary{/lang}: {$character->getPrimaryCharacter()->getTitle()})
                                </span>
                            {/if}

                            <span class="characterStatusIcons">
                                {if $character->isDisabled}
                                    <span class="jsTooltip jsCharacterIsDisabled" title="{lang}rp.acp.character.isDisabled{/lang}">
                                        {icon name='power-off'}
                                    </span>
                                {/if}
                            </span>
                        </td>
                        <td class="columnText columnUsername">{$character->username}</td>
                        <td class="columnDate columnCreated">{time time=$character->created}</td>

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

        {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}
                {if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
                    <li>
                        <a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
                            {icon name='plus'}
                            <span>{lang}rp.character.add{/lang}</span>
                        </a>
                    </li>
                {/if}

                {event name='contentFooterNavigation'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </footer>

    <script data-relocate="true">
        require(['WoltLabSuite/Core/Controller/Clipboard', 'MDRP/Acp/Ui/Character/Editor'], (
            ControllerClipboard, { AcpUiCharacterEditor }) => {
            ControllerClipboard.setup({
                pageClassName: 'rp\\acp\\page\\CharacterListPage',
                hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
            });

            new AcpUiCharacterEditor();
        });
    </script>
{else}
    <woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}