
/*
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

function showTab() {
	var selectedID = $(this).attr('href');
	var newTab = $("#" + selectedID.substring(1));
	$(".show").addClass("hide");
	$(".show").removeClass("show");
	newTab.removeClass("hide");
	newTab.addClass("show");
}

$(document).ready(function() {
	$("ul.tablist a").click(showTab);
	$("div.tab-wrapper>div.tabs").addClass("hide");
	$("div.tab-wrapper>div.tabs:first").removeClass("hide");
	$("div.tab-wrapper>div.tabs:first").addClass("show");
});
