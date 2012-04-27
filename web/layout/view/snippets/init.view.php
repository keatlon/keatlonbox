<script type="text/javascript" >
	$(document).ready( function() {
		application.configure( <?= json_encode(conf::$conf['client']) ?> );
		application.dispatch( <?= json_encode(response::get()) ?> );
	});
</script>