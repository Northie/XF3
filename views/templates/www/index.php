<html>
	<head>
		<style>
			*, html, html body, body * {
				font-family: sans-serif;
				font-size: 12px;
			}
			
			th, td {
				text-align: left;
				vertical-align: top;
				padding:4px;
			}
			
			table, td, th {
				border-collapse: collapse;
				border:1px solid silver;
			}
			
		</style>
	</head>
	<body>
<?php

function array2table($array) {
	$c = "<table>\n";
	foreach($array as $key => $val) {
		$c.="<tr><th>$key</th><td>\n";
		if(is_array($val)) {
			$c.=array2table($val);
		} else {
			$c.=$val;
		}
		$c.="\n</td>\n</tr>\n";
	}
	$c.="</table>\n\n";
	
	return $c;
}

echo array2table($this->data);
?>
	</body>
</html>