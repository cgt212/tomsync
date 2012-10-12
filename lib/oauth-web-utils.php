<?
function arrayofarrays2table(&$subject, $prefix) {
	echo "<table id=\"$prefix-table\">\n";
	echo "  <tr class=\"$prefix-header-row\">\n";
	foreach($subject[0] as $key => $value) {
		echo "    <th class=\"$prefix-header-col\">$key</td>\n";
	}
	echo "  </tr>\n";
	foreach($subject as &$arr) {
		echo "  <tr class=\"$prefix-row\">\n";
		foreach($arr as $val) {
			echo "    <td class=\"$prefix-col\">$val</td>\n";
		}
		echo "  </tr>\n";
	}
	echo "</table>\n";
}
?>
