<script type="text/javascript">

function change(which) {

    var origCursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    var tabs = new Array();

    tabs.push('core_tab');
    tabs.push('standard_tab');
    tabs.push('custom_tab');
    tabs.push('plugins_tab');
    tabs.push('preview_tab');

    for ( var i = 0; i < tabs.length; i++ ) {
        var tabStyle = document.getElementById(tabs[i]).style;
        var tabButton = document.getElementById(tabs[i] + '_btn');
        if (tabs[i] != which) {
            tabStyle.display = 'none';
            tabButton.className = '';
        }
    }

    document.getElementById(which).style.display = 'block';
    document.getElementById(which + '_btn').className = 'current';

    document.body.style.cursor = origCursor;
}

function change_all_udm_blocks( setting ) {
	if (!setting>'') {setting='block';}
	var block_set=document.getElementsByTagName('div');
	for (i=0;i<block_set.length; i++) {
		if ( block_set.item(i).className == 'hidden' ) {
			var parentDiv = document.getElementById(block_set.item(i).id + "_parent");
			//summary = summary + parentDiv.id + " : " + block_set.item(i).style.display + " vs " + setting + "\n";
			if ( block_set.item(i).style.display != setting ) {
				changef( block_set.item(i).id );
			}
		}
	}
}

function changef(which) {
    var setting = document.getElementById(which).style.display;
    if ( setting == 'block' ) {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset standard_tab';
        document.getElementById(which).style.display = 'none';
        document.getElementById( "arrow_" + which ).src = 'images/arrow-right.gif';
    } else {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset standard_tab fieldextra';
        document.getElementById(which).style.display = 'block';
        document.getElementById( "arrow_" + which ).src = 'images/arrow-down.gif';
    }
}

function addPlugin() {

    var namespace_sel = document.getElementsByName('plugin_add[0]')[0];
    var action_sel    = document.getElementsByName('plugin_add[1]')[0];

    var namespace     = namespace_sel.options[namespace_sel.value].text
    var action        = action_sel.options[action_sel.value].text

    var add_button    = document.getElementById('plugin_add_btn');

    var newField = document.createElement("input");
    newField.setAttribute("type", "hidden");
    newField.setAttribute("name", "plugin_add_" + namespace + "_" + action);

    var newFieldDisplay = document.createElement("div");
    newFieldDisplay.innerHTML = namespace + "/" + action + " scheduled for addition.";

    //alert(add_button.form.name);
    add_button.form.appendChild(newField);
    pNode = add_button.parentNode;
    pNode.parentNode.insertBefore(newFieldDisplay, pNode.nextSibling);

}
</script>

<style type="text/css">
    div#formContainer {
        border: 1px solid black;
        padding: 1ex;
		margin-top: 0px;
    }

    div.tab {
        display: none;
    }

    #core_tab {
        display: block;
    }

    p.field {
        padding: 0.5ex;
        vertical-align: middle;
    }

    div.tab p.field {
        clear: left;
    }

    div.tab div.fieldset p.field {
        clear: none;
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

    div.fieldset p.field {
        float: left;
        clear: none;
    }

    .fieldset label {
        width: auto;
        text-align: left;
        font-weight: normal;
        padding-right: 0;
        display: inline;
    }

    div.fieldset {
        clear: both;
        padding: 0;
    }

    div.fieldset * {
        margin: 0;
    }

    div.hidden {
        clear: both;
    }

    div.fieldset p.title {
        min-width: 240;
    }

    div.hidden {
        display: none;
        margin: 0;
        padding: 0;
    }

    .hidden p.field {
        display: block;
        float: left;
        clear: none;
        vertical-align: top;
        margin: 5px 0;
        width: 256;
    }

    .hidden p.field * {
        float: left;
        clear: left;
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
        margin: 0 0.25ex -5px;
        padding-top: 10px;
        vertical-align: bottom;
    }

    li.buttons {
        padding-top: 0px;
        padding-bottom: 6px;
        margin-bottom: 6px;
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

<form <?= $this->form['attributes'] ?>>

<div id="tabs">
    <ul id="topnav">
        <li class="tab1"><span class="current" id="core_tab_btn" onclick="change('core_tab');" >Settings</span></li>
        <li class="tab2"><span id="standard_tab_btn" onclick="change('standard_tab');">Standard Fields</span></li>
        <li class="tab3"><span id="custom_tab_btn" onclick="change('custom_tab');">Custom Fields</span></li>
        <li class="tab4"><span id="plugins_tab_btn" onclick="change('plugins_tab');">Plugins</span></li>
        <li class="tab5"><span id="preview_tab_btn" onclick="change('preview_tab');">Preview</span></li>
        <li class="buttons" style="float: right;"><?php foreach ($this->form['sections'][0]['elements'] as $e): ?><?= $e['html'] ?><?php endforeach; ?></li>
    </ul>
</div>

<br style="clear: both;" />

<?= $this->form['javascript'] ?>

<div id="formContainer">

<?php foreach ( $this->form['sections'] as $section ): ?>

    <?php if (!isset($section['name']) || $section['name'] == '') continue; ?>

    <div id="<?= $section['name'] ?>" class="tab">

        <?php foreach ( $section['elements'] as $e ): ?>

            <?php if (strpos($e['name'], 'arrow') !== false) { ?>

                <div class="fieldset" id="<?= substr($e['name'], 6) ?>_parent">

            <?php } elseif (strpos($e['name'], 'type') !== false) { ?>

                <div id="<?= substr($e['name'], 5) ?>" class="hidden">

            <?php } ?>

            <?php if ($e['name'] == 'plugin_add_btn') { ?>

                <p style="clear: none;">
                    <?= $e['html'] ?>
                </p>

            <?php } else { ?>

            <p class="field<?= (strpos($e['name'],'title') !== false) ? ' title' : '' ?>">
                <label for="<?= $e['name'] ?>"><?=$e['label']?></label> <?= $e['html'] ?>
            </p>

            <?php } ?>

            <?php if (strpos($e['name'], 'size') !== false): ?>

                <br style="clear: both;" />
                </div>
                <br style="clear: both;" />
                </div>

            <?php endif; ?>

        <?php endforeach; ?>

        <br style="clear: both;" />

    </div>

<?php endforeach; ?>

</div>

</form>
