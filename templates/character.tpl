{capture assign='pageTitle'}{$character->characterName} - {lang}rp.character.characters{/lang}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader characterProfileCharacter" data-object-id="{$character->characterID}">
        <div class="contentHeaderIcon">
            {if $character->userID == $__wcf->user->userID}
                <a href="{link application='rp' controller='CharacterEdit' id=$character->characterID}{/link}" class="jsTooltip"
                    title="{lang}rp.character.edit{/lang}">
                    {unsafe:$character->getAvatar()->getImageTag(128)}
                </a>
            {else}
                <span>{unsafe:$character->getAvatar()->getImageTag(128)}</span>
            {/if}
        </div>

        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                <span class="characterProfileUsername">{$character->characterName}</span>

                {event name='afterContentTitle'}
            </h1>

            <div class="contentHeaderDescription">
                <ul class="inlineList commaSeparated">
                    {if $__wcf->getSession()->getPermission('user.rp.canViewCharacterProfile')}
                        {if !$character->guildName|empty}<li>{$character->guildName}</li>{/if}
                    {/if}
                    <li>{lang}rp.character.charactersList.created{/lang}</li>

                    {event name='characterHeaderDescription'}
                </ul>

                <dl class="plain inlineDataList">
                    {include file='characterInformationStatistics' application='rp'}

                    {if $character->views}
                        <dt>{lang}rp.character.views{/lang}</dt>
                        <dd>{#$character->views}</dd>
                    {/if}
                </dl>
            </div>
        </div>

        {hascontent}
        <nav class="contentHeaderNavigation">
            <ul class="userProfileButtonContainer">
                {content}
                {if $character->userID == $__wcf->user->userID}
                    <li>
                        <a href="{link controller='CharacterEdit' application='rp' object=$character}{/link}" class="button">
                            {icon name='pencil'}
                            <span>{lang}rp.character.edit{/lang}</span>
                        </a>
                    </li>
                {/if}

                {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </header>
{/capture}

{include file='header'}

{if !$menus|empty}
    <div id="profileContent" class="section tabMenuContainer characterProfileContent">
        <nav class="tabMenu">
            <ul>
                {foreach from=$menus item=menu}
                    {if $menu->isVisible($characterID)}
                        <li>
                            <a href="#{$menu->getID()|rawurlencode}">
                                {$menu->getName()}
                            </a>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </nav>

        {foreach from=$menus item=menu}
            {if $menu->isVisible($characterID)}
                <div id="{$menu->getID()}" class="tabMenuContent">
                    {unsafe:$menu->getContent($characterID)}
                </div>
            {/if}
        {/foreach}
    </div>
{/if}

{include file='footer'}