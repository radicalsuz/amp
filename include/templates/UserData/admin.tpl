{literal}
<script type="text/javascript">

function change(which) {

    var origCursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    var tabs = new Array();

    tabs.push('udm_core');
    tabs.push('udm_standard');
    tabs.push('udm_custom');
    tabs.push('udm_plugins');
    tabs.push('udm_preview');

    for ( var i = 0; i < tabs.length; i++ ) {
        var tabStyle = document.getElementById(tabs[i]).style;
        var tabButton = document.getElementById(tabs[i] + '_tab');
        if (tabs[i] != which) {
            tabStyle.display = 'none';
            tabButton.className = '';
        }
    }

    document.getElementById(which).style.display = 'block';
    document.getElementById(which + '_tab').className = 'current';

    document.body.style.cursor = origCursor;
}

function changef(which) {
    var setting = document.getElementById(which).style.display;
    if ( setting == 'block' ) {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset udm_standard';
        document.getElementById(which).style.display = 'none';
        document.getElementById( "arrow_" + which ).src = 'images/arrow-right.gif';
    } else {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset udm_standard fieldextra';
        document.getElementById(which).style.display = 'block';
        document.getElementById( "arrow_" + which ).src = 'images/arrow-down.gif';
    }
}
</script>

<style type="text/css">
    div#formContainer {
        border: 1px solid black;
        padding: 1ex;
    }

    div.tab {
        display: none;
    }

    p.field {
        padding: 0.5ex;
        clear: left;
        vertical-align: middle;
    }

    .field label, .field input, .field textarea, .field select {
        float: left;
    }

    .field label {
        width: 12em;
        text-align: right;
        font-weight: bold;
        padding-right: 1em;
    }

    div#tabs ul {
        margin: 0;
        padding: 0 0 2px 0.5em;
    }

    div#tabs ul li span {
        padding: 0.2ex 0.5ex;
        background-color: #e6e6e6;
        border: 1px solid black;
        cursor: pointer;
    }

    div#tabs ul li span.current {
        background-color: white;
        border-bottom: 1px solid white;
        font-weight: bold;
    }

    div#tabs ul li {
        margin: 0 0.25ex;
    }

    label.field_title {
        width: 20em;
        float: left;
        font-weight: bold;
    }

    label.field_title span.fieldname {
        font-weight: normal;
    }

    img.field_arrow {
        float: left;
        clear: both;
        margin-right: 0.25ex;
        cursor: pointer;
    }

    div.fieldextra {
        border: 1px solid grey;
        background: #fffff0;
        padding: 0.5em;
        margin: 1em 0;
    }

    div.hidden_fields {
        display: none;
        padding-top: 0.5em;
    }

    div.hidden_field_group {
        float: left;
        width: 20em;
        padding: 0.5ex;
    }

    div#editbox {
        float: right;
        margin-right: 0.75em;
        width: 250px;
        border: 1px solid black;
        background: #99CCFF;
    }

    div#editbox iframe {
        width: 250px;
        height: 100px;
    }

</style>
{/literal}

<div id="tabs">
    <ul id="topnav">
        <li class="tab1"><span class="current" id="udm_core_tab" onclick="change('udm_core');" >Settings</span></li>
        <li class="tab2"><span id="udm_standard_tab" onclick="change('udm_standard');">Standard Fields</span></li>
        <li class="tab3"><span id="udm_custom_tab" onclick="change('udm_custom');">Custom Fields</span></li>
        <li class="tab4"><span id="udm_plugins_tab" onclick="change('udm_plugins');">Plugins</span></li>
        <li class="tab5"><span id="udm_preview_tab" onclick="change('udm_preview');">Preview</span></li>
    </ul>
</div>

<br style="clear: both;" />

{$form.javascript}

<div id="formContainer">

<form {$form.attributes}>

{$form.hidden}

{foreach from=$form key=key_val item=item_val}

  {$key_val}: {$item_val.type}<br/>
<!--
  <div id="{$curr_section.name}" class="tab" style="display: block;">
    {$curr_section.name}
  </div>
-->

{/foreach}

</form>

</div>
