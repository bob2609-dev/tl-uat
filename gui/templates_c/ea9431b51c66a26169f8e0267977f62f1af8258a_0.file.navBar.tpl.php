<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:08
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\navBar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e578a02bf8_61983562',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ea9431b51c66a26169f8e0267977f62f1af8258a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\navBar.tpl',
      1 => 1772654118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
  ),
),false)) {
function content_69a9e578a02bf8_61983562 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),1=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.truncate.php','function'=>'smarty_modifier_truncate',),));
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>"title_events,event_viewer,home,testproject,title_specification,title_execute,
             title_edit_personal_data,th_tcid,link_logout,title_admin,
             search_testcase,title_results,title_user_mgmt,full_text_search"),$_smarty_tpl ) );?>

<?php $_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(basename($_smarty_tpl->source->filepath),".tpl",''));
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
?>
</head>

<body style="min-width: 800px;">
  <div style="float:left; height: 100%;">
    <a href="index.php" target="_parent">
      <img alt="Company logo" title="logo" src="<?php echo @constant('TL_THEME_IMG_DIR');
echo $_smarty_tpl->tpl_vars['tlCfg']->value->logo_navbar;?>
" /></a>
  </div>
  <style>
    .menu_title {
      display: flex;
      align-items: center;
    }

    .project-tag {
      margin-left: auto;
      /* pushes text to far right */
      padding-right: 12px;
      /* space from edge */
      color: orange;
      font-weight: 600;
    }

    .left-section {
      display: flex;
      align-items: center;
      gap: 8px;
    }
  </style>

  <div class="menu_title">

    <span class="left-section">
      <span class="bold"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->whoami, ENT_QUOTES, 'UTF-8', true);?>
</span>

      <a href='lib/usermanagement/userInfo.php' target="mainframe" accesskey="i" tabindex="6">
        <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['account'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['title_edit_personal_data'];?>
">
      </a>

      <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->logout;?>
" target="_parent" accesskey="q">
        <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['logout'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['link_logout'];?>
">
      </a>
    </span>

    <span class="project-tag">
      LOCAL DEV INSTANCE
    </span>

  </div>

  <div class="menu_bar" style="margin: 0px 5px 0px 135px;">
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->TestProjects != '') {?>
      <div style="display: inline; float: right;">
        <form style="display:inline" name="productForm" action="lib/general/navBar.php?viewer=<?php echo $_smarty_tpl->tpl_vars['gui']->value->viewer;?>
"
          method="get">
          <?php echo $_smarty_tpl->tpl_vars['labels']->value['testproject'];?>

          <select style="font-size: 80%;position:relative; top:-1px;" name="testproject" onchange="this.form.submit();">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->TestProjects, 'tproject_name', false, 'tproject_id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tproject_id']->value => $_smarty_tpl->tpl_vars['tproject_name']->value) {
?>
              <option value="<?php echo $_smarty_tpl->tpl_vars['tproject_id']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tproject_name']->value, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['tproject_id']->value == $_smarty_tpl->tpl_vars['gui']->value->tprojectID) {?>
                selected="selected" <?php }?>>
                <?php echo htmlspecialchars(smarty_modifier_truncate($_smarty_tpl->tpl_vars['tproject_name']->value,$_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'TESTPROJECT_TRUNCATE_SIZE')), ENT_QUOTES, 'UTF-8', true);?>
</option>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
          </select>
        </form>
      </div>
    <?php }?>
    <?php echo $_smarty_tpl->tpl_vars['session']->value['testprojectTopMenu'];?>


    <?php if ($_smarty_tpl->tpl_vars['gui']->value->tprojectID) {?>
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->view_testcase_spec == "yes") {?>
        <form style="display:inline" target="mainframe" name="searchTC" id="searchTC" action="lib/testcases/archiveData.php"
          method="get">
          <input style="font-size: 80%; position:relative; top:-1px;" type="text" size="<?php echo $_smarty_tpl->tpl_vars['gui']->value->searchSize;?>
"
            title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['search_testcase'];?>
" name="targetTestCase" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->tcasePrefix;?>
" />

                    <input type="hidden" id="tcasePrefix" name="tcasePrefix" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->tcasePrefix;?>
" />

                    <input type="hidden" id="caller" name="caller" value="navBar" />
          <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['magnifier'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['search_testcase'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['labels']->value['search_testcase'];?>
"
            onclick="jQuery('#searchTC').submit()" class="clickable" style="position:relative; top:2px;" />
          <input type="hidden" name="edit" value="testcase" />
          <input type="hidden" name="allow_edit" value="0" />
        </form>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->view_testcase_spec == "yes") {?>
        <form style="display:inline" target="mainframe" name="fullTextSearch" id="fullTextSearch"
          action="lib/search/searchMgmt.php" method="post">
          <input type="hidden" name="caller" value="navBar">
          <input type="hidden" name="tproject_id" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
">

          <input style="font-size: 80%; position:relative; top:-1px;" type="text" size="50"
            title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['full_text_search'];?>
" name="target" value="" />

          <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['magnifier'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['full_text_search'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['labels']->value['full_text_search'];?>
"
            onclick="jQuery('#fullTextSearch').submit()" class="clickable" style="position:relative; top:2px;" />
        </form>
      <?php }?>

    <?php }?>
  </div>

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_TITLE_BAR']) {?>
    <div align="center">
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_TITLE_BAR'], 'menu_item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['menu_item']->value) {
?>
        <?php echo $_smarty_tpl->tpl_vars['menu_item']->value;?>

      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->updateMainPage == 1) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
      parent.mainframe.location = "<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
lib/general/mainPage.php";
    <?php echo '</script'; ?>
>
  <?php }?>

</body>

</html><?php }
}
