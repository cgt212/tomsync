<?php

/*
 * Bootstrap file for some of the web files of TomSync
 *
 * Copyright 2012 Chris Tsonis <cgt212@whatbroke.com>
 *
 * This file is part of TomSync.
 *
 * TomSync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TomSync is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TomSync.  If not, see <http://www.gnu.org/licenses/>.
 */

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
