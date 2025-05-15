{if $canParticipate}
    <li>
        <a href="#" class="button buttonPrimary jsEventRaidParticipate"
            data-add-participant="{link application='rp' controller='AddParticipant' id=$event->eventID}{/link}"
            data-event-id="{$event->eventID}" data-has-attendee="{if $hasAttendee}1{else}0{/if}" {if $hasAttendee}
        style="display: none;" {/if}>
        {icon name='plus'}
        <span>{lang}rp.event.raid.participate{/lang}</span>
    </a>
</li>

<script data-relocate="true">
    require(['MDRP/Ui/Event/Raid/Participant/Add'], function({ setup }) {
        setup(document.querySelector('.jsEventRaidParticipate'));
    });
</script>
{/if}