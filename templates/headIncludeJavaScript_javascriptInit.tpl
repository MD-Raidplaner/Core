require(['MDRP/BootstrapFrontend'], function({ setup }) {
    {jsphrase name='rp.character.selection'}
    {jsphrase name='rp.event.raid.container.login'}
    {jsphrase name='rp.event.raid.container.logout'}
    {jsphrase name='rp.event.raid.container.reserve'}
    {jsphrase name='rp.event.raid.status'}
    {jsphrase name='rp.event.raid.updateStatus'}

    setup({
        RP_API_URL: '{$__wcf->getPath('rp')}',
    });

    document.body.setAttribute('rp-game', '{$__rp->getGame()->identifier}');
});