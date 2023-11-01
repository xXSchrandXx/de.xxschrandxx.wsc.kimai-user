{include file='header' pageTitle='wcf.acp.page.kimaiUserList.title'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.page.kimaiUserList.title{/lang}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		{include file='__kimaiUsersResetButtons'}
		{event name='contentHeaderNavigation'}
	</nav>
</header>

{if $domains|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th>{lang}wcf.acp.page.kimaiUserList.{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$users item=user}
					<tr>
						<td class="columnTitle">{$user['name']}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	<nav class="contentFooterNavigation">
		{include file='__kimaiUsersResetButtons'}
		{event name='contentFooterNavigation'}
	</nav>
</footer>

{include file='footer'}
