{if $action == 'edit'}
    {capture assign='contentDescription'}{$formObject->getTitle()}{/capture}
{/if}

{include file='header'}

{if $action == 'add'}
    {if !$__wcf->session->getPermission('user.rp.canCreateEventWithoutModeration')}
        <woltlab-core-notice type="warning">{lang}rp.event.moderation.info{/lang}</woltlab-core-notice>
    {/if}
{/if}

{unsafe:$form->getHtml()}

{include file='footer'}