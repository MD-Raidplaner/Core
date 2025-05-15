<mdrp-attendee-drag-and-drop-box class="contentItem attendeeBox" distribution-id="{$__availableDistributionID}"
    droppable="distribution{$__availableDistributionID}" status="{$__status}">
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
        {if $attendees[$__status][$__availableDistributionID]|isset}
            {foreach from=$attendees[$__status][$__availableDistributionID] item=attendee}
                {include application='rp' file='eventRaidAttendeeItems'}
            {/foreach}
        {/if}
    </div>
</mdrp-attendee-drag-and-drop-box>