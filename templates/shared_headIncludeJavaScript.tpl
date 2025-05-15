<script data-relocate="true">
    require(['MDRP/BootstrapFrontend'], function(BootstrapFrontend) {
        {jsphrase name='rp.character.selection'}
        {jsphrase name='rp.event.raid.container.login'}
        {jsphrase name='rp.event.raid.container.logout'}
        {jsphrase name='rp.event.raid.container.reserve'}
        {jsphrase name='rp.event.raid.status'}
        {jsphrase name='rp.event.raid.updateStatus'}

        BootstrapFrontend.setup({
            endpointCharacterPopover: '{link application="rp" controller='CharacterPopover'}{/link}',
            RP_API_URL: '{$__wcf->getPath('rp')}',
        });

        document.body.setAttribute('rp-game', '{$__rp->getGame()->identifier}');
    });
</script>