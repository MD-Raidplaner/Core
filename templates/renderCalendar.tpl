<div class="rp-calendar">
    <h2 class="rpCalendar__month">{$monthName} {$year}</h2>

    <div>
        <div class="rpCalendar__weekdays">
            {foreach from=$weekDays item=weekDay}
                <div class="rpCalendar__weekday">{lang}wcf.date.day.{$weekDay}{/lang}</div>
            {/foreach}
        </div>

        <div class="rpCalendar__days">
            {foreach from=$days item=day}
                {if $day|empty}
                    <div class="rpCalendar__day rpCalendar__day__empty"></div>
                {else}
                    <div class="rpCalendar__day" data-day="{$day->__toString()}">
                        <div>{$day->getDay()}</div>
                        {if $day->getEvents()}
                            <ul class="rpCalendar__events">
                                {foreach from=$day->getEvents() item=dayEvent}
                                    <li class="rpCalendar__event rpEventPopover pointer{if $dayEvent->isFullDay} rpCalendar__event__full__day{/if}"
                                        data-event-link="{$dayEvent->getEvent()->getLink()}">
                                        {if ($dayEvent->getStatus() === 1)}
                                            {icon name='right-from-bracket'}
                                            <span class="rpCalendar__event_time">{$dayEvent->getFormattedStartTime(true)}</span>
                                        {elseif $dayEvent->getStatus() === 2}
                                            {icon name='left-right'}
                                        {elseif $dayEvent->getStatus() === 3}
                                            {icon name='right-to-bracket'}
                                            <span class="rpCalendar__event_time">{$dayEvent->getFormattedEndTime(true)}</span>
                                        {else}
                                            {if !$dayEvent->isFullDay}
                                                <span class="rpCalendar__event_time">{$dayEvent->getFormattedStartTime(true)}</span>
                                            {/if}
                                        {/if}
                                        <span>{$dayEvent->getTitle()}</span>
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
</div>

<script data-relocate="true">
    require(['WoltLabSuite/Core/Helper/Selector'], function({ wheneverSeen }) {
        wheneverSeen("[data-event-link]", (element) => {
            element.addEventListener("click", () => {
                window.location = element.dataset.eventLink;
            });
        });
    });
</script>