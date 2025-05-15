{capture assign='pageTitle'}{$raid->getTitle()} - {lang}rp.raid.list{/lang}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader rpRaid">
        <div class="contentHeaderIcon">
            {if $__wcf->session->getPermission('mod.rp.canAddRaid')}
                <a href="{link application='rp' controller='RaidEdit' id=$raid->getObjectID()}{/link}" class="jsTooltip"
                    title="{lang}rp.raid.edit{/lang}">
                    {unsafe:$raid->getIcon(64)}
                </a>
            {else}
                {unsafe:$raid->getIcon(64)}
            {/if}
        </div>
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                <span class="rpRaidTitle">{$raid->getTitle()}</span>
            </h1>

            <div class="contentHeaderDescription">
                <ul class="inlineList commaSeparated">
                    <li>{lang}rp.raid.created{/lang} {$raid->addedBy}</li>
                    <li>{lang}rp.raid.points{/lang}: {#$raid->points}</li>
                    {if $raid->notes}
                        <li>{lang}rp.raid.notes{/lang}: {$raid->notes}</li>
                    {/if}
                </ul>
            </div>
        </div>

        {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                {if $__wcf->getSession()->getPermission('mod.rp.canEditRaid')}
                    <li>
                        <a href="{link controller='RaidEdit' application='rp' id=$raid->getObjectID()}{/link}" class="button">
                            {icon name='pencil'}
                            <span>{lang}rp.raid.edit{/lang}</span>
                        </a>
                    </li>
                {/if}

                {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </header>
{/capture}

{include file='header'}

<div class="section sectionContainerList">
    <ol class="containerList raidClassDistribution doubleColumned">
        <li>
            <div id="raidChart" style="height: 400px"></div>
        </li>
        <li>
            <ol class="containerList">
                {foreach from=$classDistributions item=classDistribution}
                    <li class="box48">
                        <div>{unsafe:$classDistribution[object]->getIcon(48)}</div>

                        <div>
                            <div class="containerHeadline">
                                <h3>
                                    {implode from=$classDistribution[attendees] item=attendee}
                                    {$attendee[characterName]}
                                    {/implode}
                                </h3>
                            </div>

                            {assign var="percent" value=$classDistribution[percent]|intval}
                            {assign var="percentDecimal" value=$percent / 100}
                            <div class="rpProgressBarContainer">
                                <span style="--percent: {$percentDecimal};">{$classDistribution[percent]}%</span>
                                <div class="progressBar" style="width: {$classDistribution[percent]}%"></div>
                            </div>
                        </div>
                    </li>
                {/foreach}
            </ol>
        </li>
    </ol>
</div>

{include file='footer'}

<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.js"></script>
<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.pie.js"></script>
<script data-relocate="true">
    $(function() {
        var data = [
            {implode from=$classDistributions item=classDistribution}
            {
                label: '{$classDistribution[object]->getTitle()|encodeJS} ({$classDistribution[count]} - {$classDistribution[percent]}%)',
                data: {$classDistribution[count]}
            }
            {/implode}
        ];

        var raidChart = $("#raidChart");
        $.plot(raidChart, data, {
            series: {
                pie: {
                    innerRadius: 0.5,
                    show: true
                }
            }
        });
    });
</script>