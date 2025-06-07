{capture assign='pageTitle'}{$event->getTitle()}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader rpEventHeader">
        <div class="contentHeaderIcon">
            {unsafe:$event->getIcon(64)}
        </div>

        {include application='rp' file='eventContentHeaderTitle'}

        {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                {unsafe:$event->getType()->getContentHeaderNavigation()}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </header>
{/capture}

{capture assign='contentInteractionShareButton'}
    <button type="button" class="button small wsShareButton jsTooltip" title="{lang}wcf.message.share{/lang}"
        data-link="{$event->getLink()}" data-link-title="{$event->getTitle()}"
        data-bbcode="[rpEvent]{$event->getObjectID()}[/rpEvent]">
        {icon name='share-nodes'}
    </button>
{/capture}

{if $event->isRaidEvent()}
    {capture append='sidebarRight'}
        {hascontent}
        <section class="box" data-static-box-identifier="de.md-raidplaner.rp.event.raid.required">
            <h2 class="boxTitle">{lang}rp.event.raid.required{/lang}</h2>

            <div class="boxContent">
                <dl class="plain dataList">
                    {content}
                    {foreach from=$event->getType()->getRequirements() key=__key item=__value}
                        <dt>{lang}{$__key}{/lang}</dt>
                        <dd>{unsafe:$__value}</dd>
                    {/foreach}
                    {/content}
                </dl>
            </div>
        </section>
        {/hascontent}

        {hascontent}
        <section class="box" data-static-box-identifier="de.md-raidplaner.rp.event.raid.conditions">
            <h2 class="boxTitle">{lang}rp.event.raid.condition{/lang}</h2>

            <div class="boxContent">
                <dl class="plain dataList">
                    {content}
                    {event name='eventRaidConditions'}
                    {/content}
                </dl>
            </div>
        </section>
        {/hascontent}

        {if $event->leaders}
            <section class="box" data-static-box-identifier="de.md-raidplaner.rp.event.raid.leaders">
                <h2 class="boxTitle">
                    {lang}rp.event.raid.leader{if $event->getType()->getLeaders()|count > 1}s{/if}{/lang}
                </h2>

                <div class="boxContent">
                    <ul class="sidebarItemList">
                        {foreach from=$event->getType()->getLeaders() item=leader}
                            <li class="box24">
                                {character object=$leader type='avatar24' ariaHidden='true' tabindex='-1'}

                                <div class="sidebarItemTitle">
                                    <h3>{character object=$leader}</h3>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </section>
        {/if}
    {/capture}
{/if}

{if $event->getType()->showEventNodes('right')}
    {hascontent}
    {capture append='sidebarRight'}
        <section class="box" data-static-box-identifier="de.md-raidplaner.rp.notes">
            <h2 class="boxTitle">{lang}rp.event.notes{/lang}</h2>

            <div class="boxContent htmlContent">
                {content}
                {unsafe:$event->getSimplifiedFormattedNotes()}
                {/content}
            </div>
        </section>
    {/capture}
    {/hascontent}
{/if}

{capture assign='contentInteractionButtons'}
    {unsafe:$interactionContextMenu->render()}
{/capture}

{event name='beforeHeader'}

{include file='header'}

{event name='afterHeader'}

{if $event->getType()->showEventNodes('center')}
    {hascontent}
    <section class="section">
        <h2 class="sectionTitle">{lang}rp.event.notes{/lang}</h2>

        <dl>
            <dt></dt>
            <dd>
                <div class="htmlContent">
                    {content}
                    {$event->getSimplifiedFormattedNotes()}
                    {/content}
                </div>
            </dd>
        </dl>
    </section>
    {/hascontent}
{/if}

{if !$event->isDeleted && $event->getType()->isExpired()}
    <woltlab-core-notice type="error">{lang}rp.event.expired{/lang}</woltlab-core-notice>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{$event->getDeleteNote()}</p>
    </div>
{/if}

<div id="event{$event->eventID}" class="event" data-event-id="{$event->eventID}" data-title="{$event->getTitle()}">
    {unsafe:$event->getType()->getContent()}
</div>

<footer class="contentFooter">
    {hascontent}
    <nav class="contentFooterNavigation">
        <ul>
            {content}
            {event name='contentFooterNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</footer>

{event name='afterFooter'}

{if $previousEvent || $nextEvent}
    <div class="section event__navigation">
        {if $previousEvent}
            <div class="event__navigation__item event__navigation__item--previous event__navigation__item--withImage">
                <div class="event__navigation__item__icon">
                    {icon size=48 name='chevron-left'}
                </div>
                <div class="event__navigation__item__image">{unsafe:$previousEvent->getIcon(96)}</div>
                <div class="event__navigation__item__content">
                    <div class="event__navigation__item__entityName">{lang}rp.event.previousEvent{/lang}</div>
                    <div class="event__navigation__item__title">
                        <a href="{$previousEvent->getLink()}" rel="prev" class="event__navigation__item__link eventLink"
                            data-object-id="{$previousEvent->getObjectID()}">
                            {$previousEvent->getTitle()}
                        </a>
                    </div>
                </div>
            </div>
        {/if}
        {if $nextEvent}
            <div class="event__navigation__item event__navigation__item--next event__navigation__item--withImage">
                <div class="event__navigation__item__icon">
                    {icon size=48 name='chevron-right'}
                </div>
                <div class="event__navigation__item__image">{unsafe:$nextEvent->getIcon(96)}</div>
                <div class="event__navigation__item__content">
                    <div class="event__navigation__item__entityName">{lang}rp.event.nextEvent{/lang}</div>
                    <div class="event__navigation__item__title">
                        <a href="{$nextEvent->getLink()}" rel="next" class="event__navigation__item__link eventLink" data-object-id="{$nextEvent->getObjectID()}">
                            {$nextEvent->getTitle()}
                        </a>
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}

{event name='beforeComments'}

{unsafe:$event->getDiscussionProvider()->renderDiscussions()}

{include file='footer'}