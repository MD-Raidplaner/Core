{if $event->isCanceled}
    <woltlab-core-notice type="error">{lang}rp.event.raid.canceled{/lang}</woltlab-core-notice>
{else if $__wcf->user->userID && !$availableCharacters|count}
    <woltlab-core-notice type="error">{lang}rp.event.raid.attendee.noCharacters{/lang}</woltlab-core-notice>
{/if}

<div class="eventRaidContainer">
    {foreach from=$availableRaidStatus key=__status item=__statusName}
        <section class="section">
            <h2 class="sectionTitle">{$__statusName}</h2>

            <div class="contentItemList">
                {if $event->distributionMode === 'none'}
                    {include application='rp' file='eventRaidItems' __availableDistributionID='0' __title='rp.event.raid.participants'|language}
                {else}
                    {foreach from=$availableDistributions item=availableDistribution}
                        {include application='rp' file='eventRaidItems' __availableDistributionID=$availableDistribution->getObjectID() __title=$availableDistribution->getTitle()}
                    {/foreach}
                {/if}
            </div>
        </section>
    {/foreach}
</div>