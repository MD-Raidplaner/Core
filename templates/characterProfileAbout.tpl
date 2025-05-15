{hascontent}
{content}
{if !$notes|empty}
    <section class="section">
        <h2 class="sectionTitle">{lang}rp.character.notes{/lang}</h2>
        <dl>
            <dt></dt>
            <dd>{$notes|phrase|newlineToBreak}</dd>
        </dl>
    </section>
{/if}
{/content}
{hascontentelse}
<div class="section">
    <woltlab-core-notice type="info">{lang userID=$character->userID}rp.character.profile.about.noData{/lang}</woltlab-core-notice>
</div>
{/hascontent}