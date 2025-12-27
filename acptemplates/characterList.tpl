{include file='header' pageTitle='rp.character.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}rp.character.list{/lang}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			{if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
				<li>
					<a href="{link application='rp' controller='CharacterAdd'}{/link}" class="button">
						{icon name='plus'}
						<span>{lang}rp.character.add{/lang}</span>
					</a>
				</li>
			{/if}

			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
