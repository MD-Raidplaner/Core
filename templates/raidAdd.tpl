{capture assign='contentHeaderNavigation'}
    <li>
        <a href="{link application='rp' controller='RaidList'}{/link}" class="button">
            {icon name="list"}
            <span>{lang}rp.raid.list{/lang}</span>
        </a>
    </li>
{/capture}

{include file='header'}

{unsafe:$form->getHtml()}

{include file='footer'}