<section class="section">
    <ol class="contentItemList">
        {foreach from=$pointAccounts key=pointAccountID item=pointAccount}
            {assign var='__pointAccountID' value=$pointAccount->pointAccountID}
            <li class="contentItem contentItemMultiColumn">
                <dl class="plain dataList contentItemContent characterPointAccountList">
                    {assign var='__pointsData' value=$characterPoints[$__pointAccountID]}
                    {assign var='__statsData' value=$characterStats[$__pointAccountID]}
                    
                    <dt>{lang}rp.character.point.account.title{/lang}</dt>
                    <dd>{$pointAccount->getTitle()}</dd>

                    {include application="rp" file="pointDetails" pointsData=$__pointsData key="received" langKey="rp.character.point.account.received"}
                    {include application="rp" file="pointDetails" pointsData=$__pointsData key="issued" langKey="rp.character.point.account.issued"}
                    {include application="rp" file="pointDetails" pointsData=$__pointsData key="adjustments" langKey="rp.character.point.account.adjustments"}
                    {include application="rp" file="pointDetails" pointsData=$__pointsData key="current" langKey="rp.character.point.account.current"}

                    {include application="rp" file="raidStats" statsData=$__statsData key="raid30" langKey="rp.character.point.account.raid30"}
                    {include application="rp" file="raidStats" statsData=$__statsData key="raid60" langKey="rp.character.point.account.raid60"}
                    {include application="rp" file="raidStats" statsData=$__statsData key="raid90" langKey="rp.character.point.account.raid90"}
                    {include application="rp" file="raidStats" statsData=$__statsData key="raidAll" langKey="rp.character.point.account.raidAll"}
                </dl>
            </li>
        {/foreach}
    </ol>
</section>