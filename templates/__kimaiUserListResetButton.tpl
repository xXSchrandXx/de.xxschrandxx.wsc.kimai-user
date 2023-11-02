<ul>
	<li>
		<a 
			onclick="
				WCF.System.Confirmation.show('{jslang}wcf.global.kimai.button.users.clear.sure{/jslang}', $.proxy(function (action) {
					if (action == 'confirm')
						window.location.href = $(this).attr('href');
				}, this));
				return false;
			" 
			href="{link controller='KimaiUserListReset' isACP=0 url=$url}{/link}" class="button">
				{icon name='refresh'}
				<span>{lang}wcf.global.kimai.button.users.clear{/lang}</span>
		</a>
	</li>
</ul>
