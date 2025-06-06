<div class="contentHeaderTitle">
    <h1 class="contentTitle">
        {$event->getTitle()}
    </h1>

    <ul class="inlineList contentHeaderMetaData rpEventMetaData">
        <li>
            {icon name='clock'}
            {$event->getFormattedStartTime()} - {$event->getFormattedEndTime()}
        </li>

        <li>
            {icon name='user'}
            {user object=$event->getUserProfile()}
        </li>

        <li>
            {icon name='eye'}
            {lang}rp.event.views{/lang}
        </li>

        {if $event->getDiscussionProvider()->getDiscussionCountPhrase()}
            <li>
                {icon name='comments'}
                {if $event->getDiscussionProvider()->getDiscussionLink()}
                <a href="{$event->getDiscussionProvider()->getDiscussionLink()}">{else}<span>
                        {/if}
                        {$event->getDiscussionProvider()->getDiscussionCountPhrase()}
                        {if $event->getDiscussionProvider()->getDiscussionLink()}</a>{else}</span>{/if}
            </li>
        {/if}

        {hascontent}
        <li>
            {icon name='flag'}
            {content}
            {if $event->isNew()}
                <span class="badge green">{lang}wcf.message.new{/lang}</span>
            {/if}

            {if $event->isDeleted}
                <span class="badge red">{lang}wcf.message.status.deleted{/lang}</span>
            {/if}

            {if $event->isDisabled}
                <span class="badge green">{lang}wcf.message.status.disabled{/lang}</span>
            {/if}

            {event name='contentHeaderMetaDataFlag'}
            {/content}
        </li>
        {/hascontent}

        {event name='contentHeaderMetaData'}
    </ul>
</div>