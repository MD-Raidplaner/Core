{include application='rp' file='characterInformationHeadline'}

{if !$disableCharacterInformationButtons|isset || $disableCharacterInformationButtons != true}{include application='rp' file='characterInformationButtons'}{/if}

{capture assign='__contentInformation'}
    {event name='beforeInformations'}
    {if $contentInformation|isset}{unsafe:$contentInformation}{/if}
    {event name='afterInformations'}
{/capture}
{assign var='__contentInformation' value=$__contentInformation|trim}

{if $__contentInformation}
    <dl class="plain inlineDataList characterContentInformation">
        {unsafe:$__contentInformation}
    </dl>
{/if}

<dl class="plain inlineDataList small">
	{include application='rp' file='characterInformationStatistics'}
</dl>