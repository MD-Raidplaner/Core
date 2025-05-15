<ul class="sidebarItemList">
	{foreach from=$boxCharacters item=boxCharacter}
		<li class="box24">
			{character object=$boxCharacter type='avatar24' ariaHidden='true' tabindex='-1'}
			
			<div class="sidebarItemTitle">
				<h3>{character object=$boxCharacter}</h3>
			</div>
		</li>
	{/foreach}
</ul>