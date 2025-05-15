{include file='shared_textFormField'}

<script data-relocate="true">
    require(['MDRP/Ui/Character/Search/Input'], ({ UiCharacterSearchInput }) => {
        new UiCharacterSearchInput(document.getElementById('{$field->getPrefixedId()}'));
    });
</script>