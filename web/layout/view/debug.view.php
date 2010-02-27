<style>

#debug_panel
{
	background-color:#fff;
	border:1px solid #aaa;
	position:absolute;
	left:0px;
	bottom:0px;
	width:100%;
}
#debug_items {height:300px;overflow:auto;}
#debug_table td {background-color:#f5f5f5; color:#000; padding:5px;}

</style>

<div id="debug_panel">
	<div id="debug_content" class="p10 hidden">
        <? if ($logItems) : ?>
		<div id="debug_items">
		<table id="debug_table" cellspacing="5" width="100%">
            <? foreach($logItems as $logItem) : if (!$logItem['message']) continue; ?>
				<tr>
					<td class="w50"><?= ($logItem['time'] * 1000) ?> ms</td>
					<td class="debugitem"><?= $logItem['message'] ?></td>
				</tr>
            <? endforeach ?>
			</table>
		</div>
        <? endif ?>
	</div>
    <div class="p10" id="debug_panel_title">
        <a href="javascript:;" onclick="$('#debug_content').toggle()">debug panel</a>
        <span class="ml20" id="application_time"></span>
    </div>
</div>