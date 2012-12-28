<?php

	require_once __DIR__.'/cfg/db.config.php';
	require_once __DIR__.'/inc/db.class.php';
	require_once __DIR__.'/inc/query.class.php';
	require_once __DIR__.'/inc/checker.class.php';

	$cfg = array(
		'hostname' => db_hostname,
		'database' => db_database,
		'username' => db_username,
		'password' => db_password,
		'prefix' => db_prefix,
		'mysqli' => db_mysqli,
		'debug' => db_debug
	);
	$query = new QUERY($cfg);
	$checker = new CHECKER();

	$query->order_asc('id');
	$q = $query->select('service');

	while ($item = $query->next($q)) {
		echo $item['name'].': ';
		$checker->url($item['url']);
		$json = json_decode($checker->exec());
		if ($json === false)
			echo 'failed'."\n";
		else {
			$query->clean();
			$query->value($json);
			$query->value('service_id', $item['id']);
			$query->insert('status');
			echo 'ok'."\n";
			$query->clean();
		}
		sleep(1);
	}
?>
