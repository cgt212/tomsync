<?php

/*
 * Routing file for web based access to notes
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

//Include web startup functions to start session and load other needed files
//check to ensure the user is logged in

function _path_splitter() {
	$path = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	$ret = array();
	foreach($path as $arg) {
		if($arg != '') {
			array_push($ret, $arg);
		}
	}
	return $ret;
}

print_r(_path_splitter());
