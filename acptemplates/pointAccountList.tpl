{include file='header' pageTitle='rp.acp.point.account.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}rp.acp.point.account.list{/lang}
            <span class="badge badgeInverse">{$gridView->countRows()}</span>
        </h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='rp' controller='PointAccountAdd'}{/link}" class="button">
                    {icon name='plus'}
                    <span>{lang}rp.acp.point.account.add{/lang}</span>
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