{if !$eventControllers|empty}
    <li>
        <button class="button buttonPrimary jsStaticDialog" data-dialog-id="eventAddDialog">
            {icon name='add'}
            <span>{lang}rp.event.add{/lang}</span>
        </button>
    </li>

    <div id="eventAddDialog" class="jsStaticDialogContent" data-title="{lang}rp.event.add{/lang}">
        <form method="post" action="{link application='rp' controller='Calendar'}{/link}">
            <div class="section">
                <dl>
                    <dt>{lang}rp.event.type{/lang}</dt>
                    <dd>
                        {foreach from=$eventControllers item=controller}
                            <label>
                                <input type="radio" name="objectType" value="{$controller->objectType}"
                                    {if RP_DEFAULT_EVENT_CONTROLLER == $controller->objectType} checked{/if}>
                                {lang}rp.event.controller.{$controller->objectType}{/lang}
                            </label>
                        {/foreach}
                    </dd>
                </dl>
            </div>

            <div class="formSubmit">
                <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
            </div>
        </form>
    </div>
{/if}