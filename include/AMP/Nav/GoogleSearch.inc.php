<script type = "text/javascript">
//<!--
function GoogleSearchForm_clearBox( element ) {
    element.value="";
}
function GoogleSearchForm_checkVal( searchform ) {
    if (! searchform.elements['q'].value) return false;
    searchform.elements['q'].value = searchform.elements['q'].value + searchform.elements['ignore'].value;
    return true;
}

//-->
</script>
<form name="form_sitesearch" method="get" action="http://www.google.com/search" target="_blank" style="margin-top: 0px; margin-bottom: 0px;" onsubmit="return GoogleSearchForm_checkVal( this );">
<input name="hl" value="en" type="hidden">
<input name="ignore" value=" site:<?php print $GLOBALS['Web_url']; ?>" type="hidden">
<input name="btnG" value = "Search" type="hidden">
<input name="q" type="text" value="Search Site" size="12" onfocus="GoogleSearchForm_clearBox( this );">
</form>
