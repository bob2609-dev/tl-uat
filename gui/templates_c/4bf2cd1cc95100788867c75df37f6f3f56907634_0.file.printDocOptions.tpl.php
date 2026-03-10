<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:40
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\results\printDocOptions.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb884521b47_29408242',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4bf2cd1cc95100788867c75df37f6f3f56907634' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\results\\printDocOptions.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_ext_js.tpl' => 1,
    'file:inc_jsCheckboxes.tpl' => 1,
    'file:inc_help.tpl' => 1,
  ),
),false)) {
function content_69aeb884521b47_29408242 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>'doc_opt_title,doc_opt_guide,tr_td_show_as,check_uncheck_all_options,build,builds,onlywithuser,direct_link'),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('bResetEXTCss'=>1), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_jsCheckboxes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->loadFromChildren) {?>
  <?php echo '<script'; ?>
 type="text/javascript">
  /* space after { and before } to signal to smarty that is JS => do not process */
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
              loader:"", enableDD:false, dragDropBackEndUrl:'',children:"" };
  <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript">
  treeCfg.root_name = '<?php echo strtr($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
';
  treeCfg.root_id = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
  treeCfg.root_href = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
';
  treeCfg.children = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->children;?>

  treeCfg.cookiePrefix = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
';
  <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/execTree.js'><?php echo '</script'; ?>
>

<?php } else { ?>
  <?php echo '<script'; ?>
 type="text/javascript">
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
               loader:"", enableDD:false, dragDropBackEndUrl:'' };
  <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript">
  treeCfg.loader = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->loader;?>
';
  treeCfg.root_name = '<?php echo strtr($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
';
  treeCfg.root_id = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
  treeCfg.root_href = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
';
  treeCfg.enableDD = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->dragDrop->enabled;?>
';
  treeCfg.dragDropBackEndUrl = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->dragDrop->BackEndUrl;?>
';
  treeCfg.cookiePrefix = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
';
  <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/treebyloader.js'><?php echo '</script'; ?>
>
<?php }?> 

<?php if ($_smarty_tpl->tpl_vars['gui']->value->buildInfoSet != '') {
echo '<script'; ?>
>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "100%" });
});


function showtr() {  
  jQuery('.link4build').hide();
  var selectVal = jQuery("#build_id option:selected").val();
  jQuery("#link_" + selectVal).show();
}

<?php echo '</script'; ?>
>
<?php }?>

</head>

<body>
<h1 class="title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->mainTitle, ENT_QUOTES, 'UTF-8', true);?>
 
                  <?php if ($_smarty_tpl->tpl_vars['gui']->value->showHelpIcon) {
$_smarty_tpl->_subTemplateRender("file:inc_help.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('helptopic'=>"hlp_generateDocOptions",'show_help_icon'=>true), 0, false);
}?>
                </h1>

<div style="margin: 10px; <?php if (!$_smarty_tpl->tpl_vars['gui']->value->showOptions) {?>display:none;<?php }?>" >

<form method="GET" id="printDocOptions" name="printDocOptions"
      action="lib/results/printDocument.php?type=<?php echo $_smarty_tpl->tpl_vars['gui']->value->doc_type;?>
">

  <input type="hidden" name="docTestPlanId" value="<?php echo $_smarty_tpl->tpl_vars['docTestPlanId']->value;?>
" />
  <input type="hidden" name="toggle_memory" id="toggle_memory"  value="0" />


  <?php if ($_smarty_tpl->tpl_vars['gui']->value->buildInfoSet != '') {?>
   <table>
    <tr>
     <td><label for="build"> <?php echo $_smarty_tpl->tpl_vars['labels']->value['build'];?>
</label></td>
     <td style="width:100px"> 
      <select class="chosen-select" name="build_id" id="build_id" 
              data-placeholder="<?php echo $_smarty_tpl->tpl_vars['labels']->value['builds'];?>
" onchange="showtr();">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->buildInfoSet, 'buildObj', false, 'build_id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['build_id']->value => $_smarty_tpl->tpl_vars['buildObj']->value) {
?>
          <option value="<?php echo $_smarty_tpl->tpl_vars['build_id']->value;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['buildObj']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </select>
     </td>
     <td style="width:20px">&nbsp;</td>
     <td><label for="with_user_assignment"><?php echo $_smarty_tpl->tpl_vars['labels']->value['onlywithuser'];?>
</label></td>
     <td><input type="checkbox" name="with_user_assignment" 
                id="with_user_assignment"></td>
    </tr>
   </table>

   <table>
    <?php $_smarty_tpl->_assignInScope('isFirst', 1);?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->buildInfoSet, 'buildObj', false, 'build_id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['build_id']->value => $_smarty_tpl->tpl_vars['buildObj']->value) {
?>
      <?php $_smarty_tpl->_assignInScope('dy', "display: none");?>
      <?php if ($_smarty_tpl->tpl_vars['isFirst']->value == 1) {?>
        <?php $_smarty_tpl->_assignInScope('dy', "display: block");?>
        <?php $_smarty_tpl->_assignInScope('isFirst', 0);?>
      <?php }?>
      <tr class="link4build" id="link_<?php echo $_smarty_tpl->tpl_vars['build_id']->value;?>
" style="<?php echo $_smarty_tpl->tpl_vars['dy']->value;?>
">
        <td><a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->buildRptLinkSet[$_smarty_tpl->tpl_vars['build_id']->value];?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['direct_link'];?>
</a></td>
      </tr>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <tr><td>&nbsp;</td></tr>
   </table>
  <?php }?>

  
  <table class="smallGrey" id="optionsContainer" name="optionsContainer">
    <?php
$__section_number_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gui']->value->outputOptions) ? count($_loop) : max(0, (int) $_loop));
$__section_number_0_total = $__section_number_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_number'] = new Smarty_Variable(array());
if ($__section_number_0_total !== 0) {
for ($__section_number_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index'] = 0; $__section_number_0_iteration <= $__section_number_0_total; $__section_number_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index']++){
?>
    <tr style="margin: 10px; <?php if (!$_smarty_tpl->tpl_vars['gui']->value->showOptionsCheckBoxes) {?>display:none;<?php }?>">
      <td><?php echo $_smarty_tpl->tpl_vars['gui']->value->outputOptions[(isset($_smarty_tpl->tpl_vars['__smarty_section_number']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index'] : null)]['description'];?>
</td>
      <td>
        <input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['gui']->value->outputOptions[(isset($_smarty_tpl->tpl_vars['__smarty_section_number']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index'] : null)]['value'];?>
" id="cb<?php echo $_smarty_tpl->tpl_vars['gui']->value->outputOptions[(isset($_smarty_tpl->tpl_vars['__smarty_section_number']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index'] : null)]['value'];?>
"
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->outputOptions[(isset($_smarty_tpl->tpl_vars['__smarty_section_number']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_number']->value['index'] : null)]['checked'] == 'y') {?>checked="checked"<?php }?>/>
      </td>
    </tr>
    <?php
}
}
?>

    <tr style="margin: 10px;<?php if (!$_smarty_tpl->tpl_vars['gui']->value->showOptionsCheckBoxes) {?>display:none;<?php }?>">
     <td><input type="button" id="toogleOptions" name="toogleOptions"
                onclick='cs_all_checkbox_in_div("optionsContainer","cb","toggle_memory");'
                value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_options'];?>
" /> </td>
    </tr>

    
    <tr>
    <?php if ($_smarty_tpl->tpl_vars['docType']->value == 'testspec' || $_smarty_tpl->tpl_vars['docType']->value == 'reqspec') {?>
      <td><?php echo $_smarty_tpl->tpl_vars['labels']->value['tr_td_show_as'];?>
</td>
      <td>
        <select id="format" name="format">
          <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->outputFormat,'selected'=>$_smarty_tpl->tpl_vars['selFormat']->value),$_smarty_tpl);?>

        </select>
      </td>
    <?php } else { ?>
      <td><input type="hidden" id="format" name="format" value="<?php echo $_smarty_tpl->tpl_vars['selFormat']->value;?>
" /></td>
    <?php }?>
    </tr>
  </table>
  <br> 
  <p><?php echo $_smarty_tpl->tpl_vars['labels']->value['doc_opt_guide'];?>
<br /></p>

</form>
</div>

<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>

</body>
</html><?php }
}
