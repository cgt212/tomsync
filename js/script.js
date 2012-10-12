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
