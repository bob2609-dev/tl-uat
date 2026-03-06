{*
@filesource custom_inc_head.tpl

Purpose: Include custom JavaScript and CSS files
*}

<!-- Custom JavaScript for Redmine integration -->
<script type="text/javascript" src="{$basehref}redmine_hook.js"></script>

<!-- Custom JavaScript for image display fix -->
<script type="text/javascript" src="{$basehref}image_fix.js"></script>

<!-- Custom JavaScript for auto-filling bug descriptions -->
<script type="text/javascript" src="{$basehref}gui/javascript/bug_description_autofill.js"></script>
