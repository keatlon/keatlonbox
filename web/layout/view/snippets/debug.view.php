<?
	profiler::finish(profiler::getAppId());

	$app		=	profiler::total(profiler::APPLICATION);
	$render		=	profiler::total(profiler::RENDER);
	$sql		=	profiler::total(profiler::SQL);
	$sqlItems	=	profiler::get(profiler::SQL);

?>

<a class="debug-total" onclick="$('.debug-total-items,.debug-details').toggle()" href="javascript:;"><?= ($app + $render) * 1000 ?>ms</a>
<a class="debug-details debug-app" onclick="$('.debug-app-items').toggle()" href="javascript:;">APP: <?= $app * 1000 ?>ms</a>
<a class="debug-details debug-render" onclick="$('.debug-render-items').toggle()" href="javascript:;">RENDER: <?= $render * 1000 ?>ms</a>
<a class="debug-details debug-sql" onclick="$('.debug-sql-items').toggle()" href="javascript:;">DB: <?= $sql * 1000 ?>ms</a>



<? if ($sqlItems) : ?>
<div class="debug-sql-items debug-items" style="display:none;">
	<? foreach($sqlItems as $item) : ?>
		<div>
			<?= ($item['time'] * 1000) ?>ms
			<?= $item['message'] ?>
		</div>
	<? endforeach ?>
</div>
<? endif ?>