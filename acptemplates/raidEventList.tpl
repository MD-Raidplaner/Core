{include file='header' pageTitle='rp.acp.raid.event.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}rp.acp.raid.event.list{/lang}
            <span class="badge badgeInverse">{$gridView->countRows()}</span>
        </h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='rp' controller='RaidEventAdd'}{/link}" class="button">
                    {icon name='plus'}
                    <span>{lang}rp.acp.raid.event.add{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

<div class="section">
    {unsafe:$gridView->render()}
</div>

{include file='footer'}