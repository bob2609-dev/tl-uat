<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\attachments_simple.inc.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a90bd30_25814298',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '03ed8bf79244344f3ed15eb43d4931c77fa05c64' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\attachments_simple.inc.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e59a90bd30_25814298 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'title_upload_attachment,enter_attachment_title,btn_upload_file,
             warning,attachment_title,
             display_inline,local_file,attachment_upload_ok,
             title_choose_local_file,btn_cancel,max_size_file_upload,
             allowed_files,allowed_filenames_regexp'),$_smarty_tpl ) );?>


<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'warning_delete_attachment','var'=>"warning_msg"),$_smarty_tpl ) );?>

<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'delete','var'=>"del_msgbox_title"),$_smarty_tpl ) );?>


<?php echo '<script'; ?>
 type="text/javascript">
function checkFileSize()
{
  if (typeof FileReader !== "undefined") {
    var bytes = document.getElementById('uploadedFile').files[0].size;
    if( bytes > <?php echo $_smarty_tpl->tpl_vars['gui']->value->import_limit;?>
 )
    {
      var msg = "<?php echo $_smarty_tpl->tpl_vars['labels']->value['max_size_file_upload'];?>
: <?php echo $_smarty_tpl->tpl_vars['gui']->value->import_limit;?>
 Bytes < " + bytes + ' Bytes';
      alert(msg);
      return false;
    }   
  }
  return true;
}  


var warning_delete_attachment = "<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'warning_delete_attachment'),$_smarty_tpl ) );?>
";
<?php echo '</script'; ?>
>

<?php if ($_smarty_tpl->tpl_vars['gsmarty_attachments']->value->enabled == FALSE) {?>
  <div class="messages"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'attachment_feature_disabled'),$_smarty_tpl ) );?>
<p>
  <?php echo $_smarty_tpl->tpl_vars['gsmarty_attachments']->value->disabled_msg;?>

  </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['gsmarty_attachments']->value->enabled && $_smarty_tpl->tpl_vars['attach_show_upload_btn']->value) {?>
  <?php if ($_smarty_tpl->tpl_vars['attach_show_upload_btn']->value && !$_smarty_tpl->tpl_vars['attach_downloadOnly']->value) {?>
  <div  style="text-align:left;margin:3px;background:#CDE;padding: 3px 3px 3px 3px;border-style: groove;border-width: thin;">
      <label for="uploadedFile_[<?php echo $_smarty_tpl->tpl_vars['attach_id']->value;?>
]" class="labelHolder"><?php echo $_smarty_tpl->tpl_vars['labels']->value['local_file'];?>
 </label>
      <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['activity'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['max_size_file_upload'];?>
: <?php echo $_smarty_tpl->tpl_vars['gui']->value->import_limit;?>
 Bytes)">

      <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->attachments->allowed_filenames_regexp != '') {?>
        <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['activity'];?>
" 
             title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['allowed_filenames_regexp'];
echo $_smarty_tpl->tpl_vars['tlCfg']->value->attachments->allowed_filenames_regexp;?>
">      
      <?php }?>
      <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->attachments->allowed_files != '') {?>
        <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['activity'];?>
" 
             title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['allowed_files'];
echo $_smarty_tpl->tpl_vars['tlCfg']->value->attachments->allowed_files;?>
">  
      <?php }?>


        <input type="file" name="uploadedFile[<?php echo $_smarty_tpl->tpl_vars['attach_id']->value;?>
][]" id="uploadedFile_<?php echo $_smarty_tpl->tpl_vars['attach_id']->value;?>
" multiple 
               size="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'UPLOAD_FILENAME_SIZE');?>
" />
        &nbsp;&nbsp;&nbsp;&nbsp;
  </div>
  <?php }
}
}
}
