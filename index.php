<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
	<head>
		<meta http-equiv="Content-Language" content="pt-br" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="public" />
		<meta name="robots" content="index,follow" xml:lang="pt-br" lang="pt-br" />
		<meta name="copyright" content="none" xml:lang="pt-br" lang="pt-br" />
		<meta name="rating" content="general" xml:lang="pt-br" lang="pt-br" />
		<title>Verificador de servi&ccedil;os de encurtador de link</title>
		<link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
	</head>
	<body>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<th>
					Servi&ccedil;o
				</th>
				<th>
					Tempo de resposta<sup>1</sup>
				</th>
				<th>
					Disponibilidade<sup>2</sup>
				</th>
			</tr>
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

	$query->field('service.name');
	$query->field_unescaped('AVG(status.time)');
	$query->alias('AVG(status.time)', 'average');
	$query->field_unescaped('((sum(status.status) * 100) / count(status.entry))');
	$query->alias('((sum(status.status) * 100) / count(status.entry))', 'availability');
	$query->inner_join('service', array('service.id', 'status.service_id'));
	$query->group('status.service_id');
	$query->order_asc('average');
	$q = $query->select('status');

	while ($item = $query->next($q)) {
		echo '			<tr>'."\n";
		echo '				<td>'."\n";
		echo '					'.$item['name']."\n";
		echo '				</td>'."\n";
		echo '				<td>'."\n";
		if ($item['average'] < 1) {
			$avg = round(($item['average'] * 1000), 2);
			echo '					'.number_format($avg, 2).' ms'."\n";
		} else {
			$avg = round($item['average'], 5);
			echo '					'.number_format($avg, 5).' s'."\n";
		}
		echo '				</td>'."\n";
		echo '				<td>'."\n";
		$aval = round($item['availability'], 2);
		if ($aval < 90.0)
			echo '					<span style="color: #A00">'.$aval.'%</span>'."\n";
		else if ($aval < 100.0)
			echo '					<span style="color: #AA0">'.$aval.'%</span>'."\n";
		else
			echo '					<span style="color: #0A0">'.$aval.'%</span>'."\n";
		echo '				</td>'."\n";
		echo '			</tr>'."\n";
	}
?>
		</table>
		<p>
			Observa&ccedil;&otilde;es:
			<ol>
				<li>Quanto menor o tempo de resposta, melhor.</li>
				<li>Quanto maior a disponibilidade, melhor.</li>
			</ol>
		</p>
	</body>
</html>
