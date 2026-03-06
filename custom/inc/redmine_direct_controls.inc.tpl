{* 
 * Direct Redmine Integration Controls
 * This template file adds bug creation and linking controls directly to the test execution page
 *}

{* Only show bug controls if there's a failure result *}
{if isset($args_status_code) && $args_status_code == 'f'}
<div class="redmine-direct-controls" style="margin:15px 0; padding:15px; border:1px solid #ddd; border-radius:4px; background-color:#f9f9f9;">
    <h3 style="margin-top:0; color:#333;">Redmine Bug Tracking</h3>
    
    {* Bug creation form *}
    <div class="redmine-bug-form">
        <form method="post" action="{$basehref}redmine-integration.php" target="_blank" id="redmine_bug_form">
            <input type="hidden" name="action" value="create" />
            <input type="hidden" name="testcase" value="{$args_tc_name}" />
            <input type="hidden" name="testplan" value="{if isset($gui->tplan_name)}{$gui->tplan_name}{/if}" />
            <input type="hidden" name="build" value="{if isset($gui->build_name)}{$gui->build_name}{/if}" />
            <input type="hidden" name="result" value="Failed" />
            <input type="hidden" name="execution_id" value="{$args_exec_id}" />
            
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:bold;">Bug Summary:</label>
                <input type="text" name="subject" value="[TestLink] Failed: {$args_tc_name}" 
                       style="width:100%; padding:5px; border:1px solid #ddd; border-radius:3px;" />
            </div>
            
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:bold;">Description:</label>
                <textarea name="description" 
                          style="width:100%; height:100px; padding:5px; border:1px solid #ddd; border-radius:3px;">
Test Case: {$args_tc_name}
Build: {if isset($gui->build_name)}{$gui->build_name}{/if}
Test Plan: {if isset($gui->tplan_name)}{$gui->tplan_name}{/if}
Status: Failed

Please provide details about the issue:</textarea>
            </div>
            
            <div style="text-align:right;">
                <button type="submit" class="btn btn-primary" 
                        style="background:#f0ad4e; color:white; border:none; padding:6px 12px; border-radius:3px;">
                    Create Bug in Redmine
                </button>
            </div>
        </form>
    </div>
    
    {* OR separator *}
    <div style="text-align:center; margin:15px 0; position:relative;">
        <hr style="margin:0; border:0; border-top:1px solid #ddd;" />
        <span style="position:absolute; top:-10px; background:#f9f9f9; padding:0 10px; color:#777;">OR</span>
    </div>
    
    {* Bug linking form *}
    <div class="redmine-link-form">
        <form method="post" action="{$basehref}redmine-integration.php" target="_blank" id="redmine_link_form">
            <input type="hidden" name="action" value="link" />
            <input type="hidden" name="testcase" value="{$args_tc_name}" />
            <input type="hidden" name="testplan" value="{if isset($gui->tplan_name)}{$gui->tplan_name}{/if}" />
            <input type="hidden" name="build" value="{if isset($gui->build_name)}{$gui->build_name}{/if}" />
            <input type="hidden" name="result" value="Failed" />
            <input type="hidden" name="execution_id" value="{$args_exec_id}" />
            
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:bold;">Link to Existing Bug ID:</label>
                <div style="display:flex;">
                    <input type="text" name="bug_id" placeholder="Enter bug ID number" 
                           style="flex:1; padding:5px; border:1px solid #ddd; border-radius:3px; margin-right:10px;" />
                    <button type="submit" class="btn btn-primary" 
                            style="background:#337ab7; color:white; border:none; padding:6px 12px; border-radius:3px;">
                        Link Bug
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{/if}
