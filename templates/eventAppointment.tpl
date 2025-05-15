{foreach from=$eventStatuses key=status item=users}
    <section class="section sectionContainerList">
        <header class="sectionHeader">
            <h2 class="sectionTitle">{lang}rp.event.{$status}{/lang}</h2>
        </header>

        <ol class="containerList jsEventAppointment" data-status="{$status}">
            {if !$users|empty}
                {foreach from=$users item=user}
                    {include application='rp' file='userListItem'}
                {/foreach}
            {else}
                <woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
            {/if}
        </ol>
    </section>
{/foreach}