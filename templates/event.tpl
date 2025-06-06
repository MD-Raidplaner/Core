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
                {unsafe:$event->getController()->getContentHeaderNavigation()}
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
                    {foreach from=$event->getController()->getRequirements() key=__key item=__value}
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
                    {lang}rp.event.raid.leader{if $event->getController()->getLeaders()|count > 1}s{/if}{/lang}
                </h2>

                <div class="boxContent">
                    <ul class="sidebarItemList">
                        {foreach from=$event->getController()->getLeaders() item=leader}
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

{if $event->getController()->showEventNodes('right')}
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

{if $event->getController()->showEventNodes('center')}
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

{if !$event->isDeleted && $event->getController()->isExpired()}
    <woltlab-core-notice type="error">{lang}rp.event.expired{/lang}</woltlab-core-notice>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{$event->getDeleteNote()}</p>
    </div>
{/if}

<div id="event{$event->eventID}" class="event" data-event-id="{$event->eventID}" data-title="{$event->getTitle()}">
    {unsafe:$event->getController()->getContent()}
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
    <div class="section">
        <nav>
            <ul class="eventNavigation">
                {if $previousEvent}
                    <li class="previousEventButton eventNavigationEvent eventNavigationEventWithImage">
                        <span class="eventNavigationEventIcon">
                            {icon size=48 name='chevron-left'}
                        </span>
                        <span class="eventNavigationEventImage">{$previousEvent->getIcon(96)}</span>
                        <span class="eventNavigationEventContent">
                            <span class="eventNavigationEntityName">{lang}rp.event.previousEvent{/lang}</span>
                            <span class="eventNavigationEventTitle">
                                <a href="{$previousEvent->getLink()}" rel="prev" class="eventNavigationEventLink">
                                    {$previousEvent->getTitle()}
                                </a>
                            </span>
                        </span>
                    </li>
                {/if}

                {if $nextEvent}
                    <li class="nextEventButton eventNavigationEvent eventNavigationEventWithImage">
                        <span class="eventNavigationEventIcon">
                            {icon size=48 name='chevron-right'}
                        </span>
                        <span class="eventNavigationEventImage">{$nextEvent->getIcon(96)}</span>
                        <span class="eventNavigationEventContent">
                            <span class="eventNavigationEntityName">{lang}rp.event.nextEvent{/lang}</span>
                            <span class="eventNavigationEventTitle">
                                <a href="{$nextEvent->getLink()}" rel="next" class="eventNavigationEventLink">
                                    {$nextEvent->getTitle()}
                                </a>
                            </span>
                        </span>
                    </li>
                {/if}
            </ul>
        </nav>
    </div>
{/if}

{event name='beforeComments'}

{unsafe:$event->getDiscussionProvider()->renderDiscussions()}

{include file='footer'}