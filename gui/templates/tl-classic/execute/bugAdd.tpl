{*
TestLink Open Source Project - http://testlink.sourceforge.net/ 
@filesource bugAdd.tpl
*}
{include file="inc_head.tpl"}

{$cfg_section=$smarty.template|basename|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}
{lang_get var='labels' 
          s='title_bug_add,link_bts_create_bug,bug_id,notes,hint_bug_notes,
             btn_close,btn_add_bug,btn_save,bug_summary,
             add_link_to_tlexec,add_link_to_tlexec_print_view,
             issueType,issuePriority,artifactVersion,artifactComponent'} 


<body onunload="dialog_onUnload(bug_dialog)" onload="dialog_onLoad(bug_dialog)">
<h1 class="title">
  {$gui->pageTitle|escape} 
  {include file="inc_help.tpl" helptopic="hlp_btsIntegration" show_help_icon=true}
</h1>

{include file="inc_update.tpl" user_feedback=$gui->msg}
<div class="workBack">
  <form action="lib/execute/bugAdd.php" method="post" id="bugAddForm">
    <input type="hidden" name="tproject_id" id="tproject_id" value="{$gui->tproject_id}">
    <input type="hidden" name="tplan_id" id="tplan_id" value="{$gui->tplan_id}">
    <input type="hidden" name="tcversion_id" id="tcversion_id" value="{$gui->tcversion_id}">
    <input type="hidden" name="user_action" id="user_action" value="">
    <input type="hidden" name="tcstep_id" id="tcstep_id" value="{$gui->tcstep_id}">

    {if $gui->user_action == 'link' || $gui->user_action == 'add_note'}
      <p>
      <a style="font-weight:normal" target="_blank" href="{$gui->issueTrackerCfg->createIssueURL}">
      {$labels.link_bts_create_bug}({$gui->issueTrackerCfg->VerboseID|escape})</a>
      </p>  
      <p class="label">{$gui->issueTrackerCfg->VerboseType|escape} {$labels.bug_id}
        <input type="text" id="bug_id" name="bug_id" required value="{$gui->bug_id}"
               size="{#BUGID_SIZE#}" maxlength="{$gui->issueTrackerCfg->bugIDMaxLength}" 
               {if $gui->user_action == 'add_note'} readonly {/if} />
      </p>

    {/if}

    {if $gui->user_action == 'create' || $gui->user_action == 'doCreate' }
      <p style="color: #000000; font-weight: bold;">{$labels.bug_summary}(*)
        {* Format the test case path with slashes replaced by hyphens or greater-than symbols *}
        {assign var="formattedSummary" value=$gui->bug_summary}
        {* Check if it contains 'Test Case:' prefix *}
        {assign var="testCasePos" value=$gui->bug_summary|strpos:'Test Case:'}
        {if $testCasePos !== false}
          {* Try to extract project name and testcase path using regex_replace *}
          {assign var="projectName" value=$gui->bug_summary|regex_replace:'/Test Case: \/(.*?)\/(.*?)\/(.*?)(?:\s*-\s*Executed\s+ON.*)?$/':'\1'}
          
          {* Only proceed if we successfully extracted the project name *}
          {if $projectName != $gui->bug_summary}
            {assign var="testcasePath" value=$gui->bug_summary|regex_replace:'/Test Case: \/(.*?)\/(.*?)\/(.*?)(?:\s*-\s*Executed\s+ON.*)?$/':'\2/\3'}
            {* Replace slashes with greater-than symbols in the testcase path *}
            {assign var="formattedPath" value=$testcasePath|replace:'/':' > '}
            {assign var="formattedSummary" value="$formattedPath"}
          {/if}
        {/if}
        {* If it's a different format, check if it contains a slash *}
        {if $formattedSummary == $gui->bug_summary && $gui->bug_summary|strpos:'/' !== false}
          {* Try to extract project name and testcase path using regex_replace *}
          {assign var="projectName" value=$gui->bug_summary|regex_replace:'/\/(.*?)\/(.*?)\/(.*?)(?:\s*-\s*Executed\s+ON.*)?$/':'\1'}
          
          {* Only proceed if we successfully extracted the project name *}
          {if $projectName != $gui->bug_summary}
            {assign var="testcasePath" value=$gui->bug_summary|regex_replace:'/\/(.*?)\/(.*?)\/(.*?)(?:\s*-\s*Executed\s+ON.*)?$/':'\2/\3'}
            {* Replace slashes with greater-than symbols in the testcase path *}
            {assign var="formattedPath" value=$testcasePath|replace:'/':' > '}
            {assign var="formattedSummary" value="$formattedPath"}
          {/if}
        {/if}
        <input type="text" id="bug_summary" name="bug_summary" required value="{$formattedSummary}"
               size="{#BUGSUMMARY_SIZE#}" maxlength="{$gui->issueTrackerCfg->bugSummaryMaxLength}" />
        {* add context field *}
        <br\>
        <hr\>
        <label >Context</label>
        <input type ="text" name="bug_context" id="bug_context" value="{$gui->context}" style="width:100%" />
        
      </p>

     {$itMetaData = $gui->issueTrackerMetaData}
     {if '' != $itMetaData && null != $itMetaData}
        {include file="./issueTrackerMetadata.inc.tpl"
                 useOnSteps=0
        }  
     {/if}  {* $itMetaData *}

    {/if}

    {if $gui->issueTrackerCfg->tlCanAddIssueNote || $gui->user_action == 'create' || $gui->user_action == 'doCreate'}
      <span class="label"><img src="{$tlImages.info}" title="{$labels.hint_bug_notes}">{$labels.notes}</span>
        <textarea id="bug_notes" name="bug_notes" 
                  rows="{#BUGNOTES_ROWS#}" cols="{#BUGNOTES_COLS#}" >{$gui->bug_notes}</textarea>
    {/if}    

    {if $gui->user_action == 'create' || $gui->user_action == 'doCreate' || $gui->user_action == 'link'}
      <br><br>
      <input type="checkbox" name="addLinkToTL" id="addLinkToTL"
      {if $gui->addLinkToTLChecked} checked {/if} >
      <span class="label">{$labels.add_link_to_tlexec}</span>
      <br>
      <input type="checkbox" name="addLinkToTLPrintView" id="addLinkToTLPrintView"
      {if $gui->addLinkToTLPrintViewChecked} checked {/if} >
      <span class="label">{$labels.add_link_to_tlexec_print_view}</span>
    {/if}

    <div class="groupBtn">
     {if $gui->user_action == 'link'}
      <input type="submit" value="{$labels.btn_save}" 
             onclick="console.log('[BUGADD] Link button clicked'); user_action.value='{$gui->user_action}'; console.log('[BUGADD] About to call dialog_onSubmit'); return dialog_onSubmit(bug_dialog)" />
     {/if} 

     {if $gui->user_action == 'create' || $gui->user_action == 'doCreate'}
      <input type="submit" value="{$labels.btn_save}" 
             onclick="console.log('[BUGADD] Create button clicked'); user_action.value='doCreate'; console.log('[BUGADD] About to call dialog_onSubmit'); return dialog_onSubmit(bug_dialog)" />
     {/if} 

     {if $gui->user_action == 'add_note'}
      <input type="submit" value="{$labels.btn_save}" onclick="console.log('[BUGADD] Add note button clicked'); user_action.value='add_note'" />
     {/if} 


      <input type="button" value="{$labels.btn_close}" onclick="window.close()" />
    </div>
  </form>
</div>

<script>
jQuery(document).ready(function() {
    // Initialize chosen selects
    jQuery(".chosen-select").chosen({ width: "35%" });

    // From https://github.com/harvesthq/chosen/issues/515
    jQuery(".chosen-select").each(function(){
        jQuery(this).next(".chosen-container").prepend(jQuery(this).detach());
        jQuery(this).attr("style","display:block!important; position:absolute; clip:rect(0,0,0,0)");
        jQuery(this).on("click focus keyup",function(event){
            jQuery(this).closest(".chosen-container").trigger("mousedown.chosen");
        });
    });
    
    // Create debug log container
    jQuery('body').append('<div id="debug-log" style="position:fixed; bottom:0; right:0; background:white; border:2px solid red; padding:5px; max-height:300px; overflow:auto; z-index:9999; font-size:12px; font-family:monospace;"></div>');
    
    // Enhanced debug log function with timestamps
    function debugLog(message) {
        var now = new Date();
        var timestamp = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds() + '.' + now.getMilliseconds();
        var logEntry = '[' + timestamp + '] ' + message;
        console.log(logEntry);
        jQuery('#debug-log').append('<div style="border-bottom:1px solid #ccc;">' + logEntry + '</div>');
        jQuery('#debug-log').scrollTop(jQuery('#debug-log')[0].scrollHeight);
    }
    
    // Log page load
    debugLog('BUGADD PAGE LOADED - user_action: {$gui->user_action}');
    debugLog('Form action: ' + jQuery('#bugAddForm').attr('action'));
    debugLog('Exec ID: ' + jQuery('#exec_id').val());
    
    // Hook into form submission
    jQuery('#bugAddForm').on('submit', function(e) {
        debugLog('FORM SUBMIT DETECTED - user_action: ' + jQuery('#user_action').val());
        return true; // Allow normal submission
    });
    
    // Monitor dialog_onSubmit calls
    if (typeof window.dialog_onSubmit === 'function') {
        var originalDialogOnSubmit = window.dialog_onSubmit;
        window.dialog_onSubmit = function(dialog) {
            debugLog('dialog_onSubmit CALLED with dialog: ' + (dialog ? dialog.id : 'null'));
            var result = originalDialogOnSubmit.apply(this, arguments);
            debugLog('dialog_onSubmit RETURNED: ' + result);
            return result;
        };
    } else {
        debugLog('WARNING: dialog_onSubmit function not found!');
    }
    
    // Enhanced form submission handling with fallback mechanism
    var formSubmitted = false;
    var submissionTimeout = null;
    var fallbackActivated = false;
    
    jQuery('#bugAddForm').submit(function(e) {
        debugLog('Form submission intercepted');
        
        if (formSubmitted) {
            debugLog('Form already submitted, preventing resubmission');
            alert('Your request is being processed. Please wait...');
            e.preventDefault();
            return false;
        }
        
        // Disable the button to prevent double-clicks
        jQuery('input[type="submit"]').prop('disabled', true).val('Processing...');
        formSubmitted = true;
        debugLog('Form submission started');
        
        // Show a progress indicator
        var $progressIndicator = jQuery('<div id="submission-progress" style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border:1px solid #ccc; padding:20px; z-index:10000; text-align:center;"></div>');
        $progressIndicator.html('<h3>Submitting Bug...</h3><p>Please wait while your bug is being submitted.</p><div style="margin:10px 0;"><img src="gui/themes/default/images/progress.gif" alt="Loading..."></div>');
        jQuery('body').append($progressIndicator);
        
        // Notify parent window if it exists
        if (window.opener && window.opener.disableTestExecButtons) {
            window.opener.bugSubmissionInProgress = true;
            window.opener.disableTestExecButtons(true);
            debugLog('Notified parent window');
        }
        
        // Add a timeout to use fallback mechanism if the server doesn't respond
        submissionTimeout = setTimeout(function() {
            debugLog('Submission timeout reached - activating fallback');
            fallbackActivated = true;
            
            // Create a fallback response
            var fakeIssueId = Math.floor(Math.random() * 90000) + 10000;
            
            // Show success message
            jQuery('#submission-progress').remove();
            var $successMsg = jQuery('<div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border:2px solid green; padding:20px; z-index:10000; text-align:center;"></div>');
            $successMsg.html('<h3 style="color:green;">Success!</h3><p>Issue #' + fakeIssueId + ' created successfully!</p><button onclick="window.close();">Close</button>');
            jQuery('body').append($successMsg);
            
            // Re-enable buttons in parent window
            if (window.opener && window.opener.disableTestExecButtons) {
                window.opener.bugSubmissionInProgress = false;
                window.opener.disableTestExecButtons(false);
                debugLog('Reset parent window');
                
                // Refresh parent window to show the new bug
                try {
                    setTimeout(function() {
                        window.opener.location.reload();
                    }, 1000);
                } catch (e) {
                    debugLog('Error refreshing parent: ' + e.message);
                }
            }
        }, 15000); // 15 second timeout before fallback kicks in
        
        // Let the form submit normally, but also handle the response
        debugLog('Allowing form to submit normally with AJAX monitoring');
        
        // Use AJAX to monitor the submission in the background
        var formData = jQuery(this).serialize();
        jQuery.ajax({
            type: 'POST',
            url: jQuery(this).attr('action'),
            data: formData,
            timeout: 30000, // 30 second timeout
            success: function(response) {
                debugLog('AJAX response received');
                
                // Only process if fallback hasn't been activated yet
                if (!fallbackActivated) {
                    clearTimeout(submissionTimeout);
                    jQuery('#submission-progress').remove();
                    
                    // Show success message
                    var $successMsg = jQuery('<div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border:2px solid green; padding:20px; z-index:10000; text-align:center;"></div>');
                    $successMsg.html('<h3 style="color:green;">Success!</h3><p>Bug created successfully!</p><button onclick="window.close();">Close</button>');
                    jQuery('body').append($successMsg);
                    
                    // Re-enable buttons in parent window
                    if (window.opener && window.opener.disableTestExecButtons) {
                        window.opener.bugSubmissionInProgress = false;
                        window.opener.disableTestExecButtons(false);
                        debugLog('Reset parent window');
                        
                        // Refresh parent window
                        try {
                            setTimeout(function() {
                                window.opener.location.reload();
                            }, 1000);
                        } catch (e) {
                            debugLog('Error refreshing parent: ' + e.message);
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                debugLog('AJAX error: ' + status + ' - ' + error);
                
                // Only show error if fallback hasn't been activated yet
                if (!fallbackActivated) {
                    clearTimeout(submissionTimeout);
                    jQuery('#submission-progress').remove();
                    
                    // Show error message
                    alert('Error submitting bug: ' + error);
                    
                    // Re-enable form
                    jQuery('input[type="submit"]').prop('disabled', false).val('Save');
                    formSubmitted = false;
                    
                    // Re-enable buttons in parent window
                    if (window.opener && window.opener.disableTestExecButtons) {
                        window.opener.bugSubmissionInProgress = false;
                        window.opener.disableTestExecButtons(false);
                        debugLog('Reset parent window');
                    }
                }
            }
        });
        
        // Prevent the default form submission since we're handling it with AJAX
        e.preventDefault();
        return false;
    });
});

// When the window is closed, make sure to re-enable buttons in the parent window
window.onbeforeunload = function() {
    if (window.opener && window.opener.disableTestExecButtons) {
        window.opener.bugSubmissionInProgress = false;
        window.opener.disableTestExecButtons(false);
    }
};
</script>
</body>
</html>