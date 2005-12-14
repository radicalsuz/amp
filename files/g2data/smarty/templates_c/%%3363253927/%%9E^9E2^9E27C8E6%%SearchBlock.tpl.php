<?php /* Smarty version 2.6.10, created on 2005-12-13 11:21:01
         compiled from gallery:modules/search/templates/blocks/SearchBlock.tpl */ ?>
<?php if (! isset ( $this->_tpl_vars['showAdvancedLink'] )): ?> <?php $this->assign('showAdvancedLink', 'true'); ?> <?php endif; ?>

<?php $this->_tag_stack[] = array('addToTrailer', array(), $this); $this->_reg_objects['g'][0]->addToTrailer($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat=true); while ($_block_repeat) { ob_start();?>
<script type="text/javascript">
  // <![CDATA[
  var search_SearchBlock_searchDefault = '<?php echo $this->_reg_objects['g'][0]->text(array('text' => 'Search the Gallery'), $this);?>
';
  var search_SearchBlock_input = document.getElementById('search_SearchBlock').searchCriteria;
  function search_SearchBlock_checkForm() {
    var sc = search_SearchBlock_input.value;
    if (sc == searchDefault || sc == '') {
      alert('<?php echo $this->_reg_objects['g'][0]->text(array('text' => "Please enter keywords to search."), $this);?>
');
      return false;
    } else {
      document.getElementById('search_SearchBlock').submit();
    }
  }

  function search_SearchBlock_focus() {
    if (search_SearchBlock_input.value == search_SearchBlock_searchDefault) {
      search_SearchBlock_input.value = '';
    }
  }

  function search_SearchBlock_blur() {
    if (search_SearchBlock_input.value == '') {
      search_SearchBlock_input.value = search_SearchBlock_searchDefault;
    }
  }
  // ]]>
</script>
<?php $_obj_block_content = ob_get_contents(); ob_end_clean(); echo $this->_reg_objects['g'][0]->addToTrailer($this->_tag_stack[count($this->_tag_stack)-1][1], $_obj_block_content, $this, $_block_repeat=false);} array_pop($this->_tag_stack);?>


<div class="<?php echo $this->_tpl_vars['class']; ?>
">
  <form id="search_SearchBlock" action="<?php echo $this->_reg_objects['g'][0]->url(array(), $this);?>
" method="post" onsubmit="return checkForm()">
    <div>
      <?php echo $this->_reg_objects['g'][0]->hiddenFormVars(array(), $this);?>

      <input type="hidden" name="<?php echo $this->_reg_objects['g'][0]->formVar(array('var' => 'view'), $this);?>
" value="search.SearchScan"/>
      <input type="hidden" name="<?php echo $this->_reg_objects['g'][0]->formVar(array('var' => "form[formName]"), $this);?>
" value="search_SearchBlock"/>
      <input type="text" id="searchCriteria" size="18"
	     name="<?php echo $this->_reg_objects['g'][0]->formVar(array('var' => "form[searchCriteria]"), $this);?>
"
	     value="<?php echo $this->_reg_objects['g'][0]->text(array('text' => 'Search the Gallery'), $this);?>
"
	     onfocus="search_SearchBlock_focus()"
	     onblur="search_SearchBlock_blur()"
	     class="textbox"/>
      <input type="hidden" name="<?php echo $this->_reg_objects['g'][0]->formVar(array('var' => "form[useDefaultSettings]"), $this);?>
" value="1" />
    </div>
    <?php if ($this->_tpl_vars['showAdvancedLink']): ?>
    <div>
      <a href="<?php echo $this->_reg_objects['g'][0]->url(array('arg1' => "view=search.SearchScan",'arg2' => "form[useDefaultSettings]=1",'arg3' => "return=1"), $this);?>
"
	 class="<?php echo $this->_reg_objects['g'][0]->linkId(array('view' => "search.SearchScan"), $this);?>
 advanced"><?php echo $this->_reg_objects['g'][0]->text(array('text' => 'Advanced Search'), $this);?>
</a>
    </div>
    <?php endif; ?>
  </form>
</div>
