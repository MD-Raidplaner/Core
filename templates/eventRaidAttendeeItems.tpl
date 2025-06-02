
<mdrp-attendee-drag-and-drop-item id="attendee{$attendee->attendeeID}"
    class="attendee{if $event->getController()->isLeader()} draggable{/if}" attendee-id="{$attendee->attendeeID}"
    character-id="{$attendee->characterID}" distribution="{$__availableDistribution}"
    event-id="{$attendee->eventID}" {if $event->getController()->isLeader()}draggable="true" {/if}
    droppable-to="{implode from=$attendee->getPossibleDistribution() item=distribution}distribution_{$distribution}{/implode}"
    user-id="{$attendee->getCharacter()->userID}">
    <div class="attendee__avatar">
        {character object=$attendee->getCharacter() type='avatar24' ariaHidden='true'}
    </div>

    <div class="attendee__name">
        <a href="{$attendee->getLink()}" class="rpEventRaidAttendeeLink"
            data-object-id="{$attendee->attendeeID}">
            {$attendee->getCharacter()->characterName}
        </a>
    </div>

    <div class="attendee__information">
        {if !$attendee->notes|empty}
            <span class="tooltip" title="{$attendee->notes}">
                {icon name='comment'}
            </span>
        {/if}
        {if !$attendee->characterID}
            <span class="tooltip" title="{lang}rp.event.raid.attendee.guest{/lang}">
                {icon name='user'}
            </span>
        {/if}
        {if $attendee->addByLeader}
            <span class="tooltip" title="{lang}rp.event.raid.attendee.addByLeader{/lang}">
                {icon name='plus-circle'}
            </span>
        {/if}
    </div>

    {if !$event->isCanceled && 
        !$event->isDeleted && 
        $event->startTime >= TIME_NOW &&
        $attendee->getCharacter()->userID == $__wcf->user->userID}
    <div id="attendeeOptions{$attendee->attendeeID}" class="attendee__menu dropdown">
        <button type="button" class="dropdownToggle" aria-label="{lang}wcf.global.button.more{/lang}">
            {icon name='ellipsis-vertical'}
        </button>
        <ul class="dropdownMenu">
            <li>
                <a href="#" class="attendee__option attendee__option--update-status">
                    {lang}rp.event.raid.updateStatus{/lang}
                </a>
            </li>
            {if $event->getController()->getContentData('availableCharacters')|count > 1}
                <li>
                    <a href="#" class="attendee__option attendee__option--character-switch">
                        {lang}rp.event.raid.attendee.character.switch{/lang}
                    </a>
                </li>
            {/if}
        </ul>
    </div>
    {/if}
</mdrp-attendee-drag-and-drop-item>