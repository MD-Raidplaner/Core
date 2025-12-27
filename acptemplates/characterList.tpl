{include file='header' pageTitle='rp.character.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">
			{lang}rp.character.list{/lang}
			<span class="badge badgeInverse">{#$gridView->countRows()}</span>
		</h1>
	</div>

	{hascontent}
	<nav class="contentHeaderNavigation">
		<ul>
			{content}
			{if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
				<li>
					<a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
						{icon name='plus'}
						<span>{lang}rp.character.add{/lang}</span>
					</a>
				</li>
			{/if}

			{event name='contentHeaderNavigation'}
			{/content}
		</ul>
	</nav>
	{/hascontent}
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
