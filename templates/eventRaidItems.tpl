<mdrp-attendee-drag-and-drop-box class="contentItem attendeeBox" distribution="{$__availableDistribution}"
    droppable="distribution_{$__availableDistribution}" status="{$__status}">
    <div class="contentItemLink">
        {if $availableDistribution|isset}
            <div class="contentItemImage">
                {unsafe:$availableDistribution->getIcon(16)}
            </div>
        {/if}

        <div class="contentItemContent">
            <h2 class="contentItemTitle">{$__title}</h2>
        </div>
    </div>
    <div class="attendeeList">
        {if $attendees[$__status][$__availableDistribution]|isset}
            {foreach from=$attendees[$__status][$__availableDistribution] item=attendee}
                {include application='rp' file='eventRaidAttendeeItems'}
            {/foreach}
        {/if}
    </div>
</mdrp-attendee-drag-and-drop-box>