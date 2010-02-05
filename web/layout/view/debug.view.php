<style>

#debug_panel
{
	background-color:#fff;
	border:1px solid #aaa;
	text-shadow:none;
	position:absolute;
	right:10px;
	top:10px;
}

#debug_table td {background-color:#f5f5f5; color:#000; padding:5px;}

</style>

<div id="debug_panel">
    <div class="p10 tar" id="debug_panel_title">
        <a href="javascript:;" onclick="$('#debug_content').toggle()">debug panel</a>
        <span class="ml20" id="application_time"></span>
    </div>
	<div id="debug_content" class="p10 hidden">
        <? if ($logItems) : ?>
			
		<table id="debug_table" cellspacing="5">
            <? foreach($logItems as $logItem) : if (!$logItem['message']) continue; ?>
				<tr>
					<td class="w50"><?= $logItem['time'] ?></td>
					<td class="debugitem"><?= $logItem['message'] ?></td>
				</tr>
            <? endforeach ?>
			</table>
        <? endif ?>
	</div>
</div>