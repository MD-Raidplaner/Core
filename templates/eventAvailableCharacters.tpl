<div class="section">
    <dl>
        <dt>{lang}rp.character.selection{/lang}</dt>
        <dd>
            <select name="characterID">
                {foreach from=$availableCharacters item=availableCharacter}
                    <option value="{$availableCharacter->getID()}">{$availableCharacter->getName()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
</div>