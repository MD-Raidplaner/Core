{if $commentList|count || $commentCanAdd}
	<section id="comments" class="section sectionContainerList">
		<h2 class="sectionTitle">{lang}wcf.global.comments{/lang}{if $event->comments} <span class="badge">{#$event->comments}</span>{/if}</h2>
		
        {include file='comments' commentContainerID='eventCommentList' commentObjectID=$eventID}
	</section>
{/if}
