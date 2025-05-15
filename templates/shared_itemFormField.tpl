{include file='__formFieldErrors'}

<div class="row rowColGap formGrid">
    <dl class="col-xs-12 col-md-6">
        <dt></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_itemName" class="long"
                placeholder="{lang}rp.item.itemName{/lang}">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-6">
        <dt></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_additionalData" class="long"
                placeholder="{lang}rp.item.additionalData{/lang}">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-4">
        <dt></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_pointAccount">
                <option value="">{lang}rp.item.pointAccount{/lang}</option>
                {foreach from=$field->getPointAccounts() item=__pointAccount}
                    <option value="{$__pointAccount->getObjectID()}">{$__pointAccount->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-4">
        <dt></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_character">
                <option value="">{lang}rp.item.character{/lang}</option>
                {foreach from=$field->getCharacters() item=__character}
                    <option value="{$__character->getObjectID()}">{$__character->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_points" class="long"
                placeholder="{lang}rp.item.points{/lang}">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2 text-right">
        <dt></dt>
        <dd>
            <a href="#" class="button small" id="{@$field->getPrefixedId()}_addButton">
                {lang}wcf.global.button.add{/lang}
            </a>
        </dd>
    </dl>
</div>

<div id="{@$field->getPrefixedId()}_itemList" class="rpItemList"></div>

<script data-relocate="true">
    require([
        'MDRP/Form/Builder/Field/Item'
    ],
    ({ Item }) => {
        WoltLabLanguage.registerPhrase("rp.item.form.field", '{jslang __literal=true}rp.item.form.field{/jslang}');
        {jsphrase name='rp.item.points.error.format'}

        new Item('{@$field->getPrefixedId()}', [
        {implode from=$field->getValue() item=item}
        {
            characterId: '{$item[characterID]}',
            characterName: '{$item[characterName]|encodeJS}',
            itemId: '{$item[itemID]}',
            itemName: '{$item[itemName]|encodeJS}',
            pointAccountId: '{$item[pointAccountID]}',
            pointAccountName: '{$item[pointAccountName]|encodeJS}',
            points: '{$item[points]}'
        }
        {/implode}
    ]);
    });
</script>