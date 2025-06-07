{if $__wcf->user->userID && !$event->getType()->isExpired()}
    <li>
        <button type="button" class="button jsAppointmentChange" data-event-id="{$event->eventID}">
            {icon name='pen-to-square'}
            <span>{lang}rp.event.participation{/lang}</span>
        </button>
    </li>

    <script data-relocate="true">
        require(['MDRP/Ui/Event/Appointment/Change'], function({ UiEventAppointmentChange }) {
            {jsphrase name='rp.event.participation'}

            new UiEventAppointmentChange(document.querySelector('.jsAppointmentChange'));
        });
    </script>

    <template id="appointmentChangeDialog">
        <dl>
            <dt></dt>
            <dd>
                <label>
                    <input type="radio" name="status" value="accepted"
                        {if $myStatus === "accepted" || $myStatus === ""} checked{/if}>
                    {lang}rp.event.accepted{/lang}
                </label>
                <label>
                    <input type="radio" name="status" value="maybe" {if $myStatus === "maybe"} checked{/if}>
                    {lang}rp.event.maybe{/lang}
                </label>
                <label>
                    <input type="radio" name="status" value="canceled" {if $myStatus === "canceled"} checked{/if}>
                    {lang}rp.event.canceled{/lang}
                </label>
            </dd>
        </dl>
    </template>
{/if}