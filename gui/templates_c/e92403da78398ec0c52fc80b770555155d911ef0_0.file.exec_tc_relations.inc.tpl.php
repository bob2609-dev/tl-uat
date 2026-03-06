<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\exec_tc_relations.inc.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a81c613_00683508',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e92403da78398ec0c52fc80b770555155d911ef0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\exec_tc_relations.inc.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e59a81c613_00683508 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'rel_labels','s'=>'relation_id,relation_type_extended,relation_set_by,test_case,relations,
             execution_history, execution, design'),$_smarty_tpl ) );?>



    <?php if ($_smarty_tpl->tpl_vars['argsRelSet']->value['num_relations'] > 0) {?>
    <table class="simple" width="100%">
      <tr>
        <th><nobr><?php echo $_smarty_tpl->tpl_vars['rel_labels']->value['relation_id'];?>
 / <?php echo $_smarty_tpl->tpl_vars['rel_labels']->value['relation_type_extended'];?>
</nobr></th>
        <th colspan="1"><?php echo $_smarty_tpl->tpl_vars['rel_labels']->value['test_case'];?>
</th>
      </tr>
      
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['argsRelSet']->value['relations'], 'rx');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['rx']->value) {
?>
        <?php $_smarty_tpl->_assignInScope('rel_status', $_smarty_tpl->tpl_vars['rx']->value['related_item']['status']);?>
        <tr>
          <td class="bold"><nobr><?php echo $_smarty_tpl->tpl_vars['rx']->value['id'];?>
 / <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rx']->value['type_localized'], ENT_QUOTES, 'UTF-8', true);?>
</nobr></td>
          <td>
          <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['history_small'];?>
"
               onclick="javascript:openExecHistoryWindow(<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['testcase_id'];?>
);"
               title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_history'];?>
" />
          <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['exec_icon'];?>
"
               onclick="javascript:openExecutionWindow(<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['testcase_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->build_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->platform_id;?>
);"
               title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution'];?>
" />
          <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['edit'];?>
"
               onclick="javascript:openTCaseWindow(<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['testcase_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['id'];?>
);"
               title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['design'];?>
" />            
          <a href="javascript:openTCaseWindow(<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['testcase_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['rx']->value['related_tcase']['id'];?>
)">
             <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rx']->value['related_tcase']['fullExternalID'], ENT_QUOTES, 'UTF-8', true);?>
:
             <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rx']->value['related_tcase']['name'], ENT_QUOTES, 'UTF-8', true);?>
</a>
          </td>
        </tr>
      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </table>
    <?php }
}
}
