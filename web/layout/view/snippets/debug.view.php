<?
	profiler::finish(profiler::getAppId());
	$app	=	profiler::total(profiler::APPLICATION);
	$sql		=	profiler::total(profiler::SQL);

	$sqlItems	=	profiler::get(profiler::SQL);
?>

<a class="debug-app" onclick="$('.debug-app-items').toggle()" href="javascript:;">APP: <?= $app * 1000 ?>ms</a>
<a class="debug-sql" onclick="$('.debug-sql-items').toggle()" href="javascript:;">DB: <?= $sql * 1000 ?>ms</a>



<? if ($sqlItems) : ?>
<div class="debug-sql-items debug-items hidden">
	<? foreach($sqlItems as $item) : ?>
		<div>
			<?= ($item['time'] * 1000) ?>ms
			<?= $item['message'] ?>
		</div>
	<? endforeach ?>
</div>
<? endif ?>