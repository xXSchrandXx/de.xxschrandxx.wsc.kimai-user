{capture assign='__contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				{lang}wcf.page.kimaiUserList.title{/lang}
			</h1>
		</div>

		<nav class="contentHeaderNavigation">
			<ul>
				{@$resetButton}

				{event name='contentHeaderNavigation'}
			</ul>
		</nav>
	</header>
{/capture}

{include file='header' contentHeader=$__contentHeader}

<div class="section">
	{if $users|isset && $users|count}
		<div class="section tabularBox">
			<table class="table">
				<thead>
					<tr>
						<th>{lang}wcf.page.kimaiUserList.id{/lang}</th>
						<th>{lang}wcf.page.kimaiUserList.username{/lang}</th>
						<th>{lang}wcf.page.kimaiUserList.alias{/lang}</th>
						<th>{lang}wcf.page.kimaiUserList.enabled{/lang}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$users item=user}
						<tr>
							<td class="columnId">{#$user->getId()}</td>
							<td class="columnTitle">{$user->getUsername()}</td>
							<td class="columnText">{$user->getAlias()}</td>
							<td class="columnBool">{$user->getEnabled()}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{else}
		<p class="info">{lang}wcf.global.noItems{/lang}</p>
	{/if}
</div>

<footer class="contentFooter">
	<nav class="contentFooterNavigation">
		<ul>
			{@$resetButton}

			{event name='contentFooterNavigation'}
		</ul>
	</nav>
</footer>

{include file='footer'}
