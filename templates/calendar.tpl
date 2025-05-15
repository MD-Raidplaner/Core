{capture assign="contentHeader"}
    {hascontent}
    <header class="contentHeader">
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                {include application='rp' file='eventAddButton'}

                {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
    </header>
    {/hascontent}
{/capture}

{capture assign='contentInteractionButtons'}
    <a href="{$lastMonthLink}" class="contentInteractionButton button">
        {icon name='angles-left'}
    </a>
    <a href="{$currentLink}" class="contentInteractionButton button">
        <span>{lang}wcf.date.period.today{/lang}</span>
    </a>
    <a href="{$nextMonthLink}" class="contentInteractionButton button">
        {icon name='angles-right'}
    </a>
{/capture}

{include file='header'}

{unsafe:$calendar}

{include file='footer'}