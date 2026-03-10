{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource inc_exec_controls.tpl
Purpose: draw execution controls (input for notes and results)
Author : franciscom
*}
{$ResultsStatusCode=$tlCfg->results.status_code}
{if $args_save_type == 'bulk'}
  {$radio_id_prefix = "bulk_status"}
{else}
  {$radio_id_prefix = "statusSingle"}
{/if}

<table class="no-border">
  <tr>
    <td style="text-align: center;">
      <div class="title">{$args_labels.test_exec_notes}</div>
      {$args_webeditor}
    </td>
    <td valign="top" style="width: 30%;">
      {* status of test *}
      <div class="title" style="text-align: center;">
        {if $args_save_type == 'bulk'} {$args_labels.test_exec_result} {else} &nbsp; {/if}
      </div>

      <div class="resultBox">
        {if $args_save_type == 'bulk'}
          {foreach key=verbose_status item=locale_status from=$tlCfg->results.status_label_for_exec_ui}
            <input type="radio" {$args_input_enable_mgmt} name="{$radio_id_prefix}[{$args_tcversion_id}]"
              id="{$radio_id_prefix}_{$args_tcversion_id}_{$ResultsStatusCode.$verbose_status}"
              value="{$ResultsStatusCode.$verbose_status}"
              onclick="javascript:set_combo_group('execSetResults','status_','{$ResultsStatusCode.$verbose_status}');"
              {if $verbose_status eq $tlCfg->results.default_status} checked="checked" {/if} />
            &nbsp;{lang_get s=$locale_status}<br />
          {/foreach}
        {else}
          {$args_labels.test_exec_result}&nbsp;
          <select name="statusSingle[{$tcversion_id}]" id="statusSingle_{$tcversion_id}">
            {html_options options=$gui->execStatusValues}
          </select>
        {/if}

        {if $tlCfg->exec_cfg->features->exec_duration->enabled}
          <br />
          {$args_labels.execution_duration}&nbsp;
          <input type="text" name="execution_duration" id="execution_duration" size="{#EXEC_DURATION_SIZE#}"
            onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="{#EXEC_DURATION_MAXLEN#}">
        {/if}
        {if $args_save_type == 'single'}
          <br />
          {$addBR=0}
          {if $tc_exec.assigned_user == ''}
            {$args_labels.assign_exec_task_to_me}&nbsp;
            <input type="checkbox" name="assignTask" id="assignTask" {if $gui->assignTaskChecked} checked {/if}>
            {$addBR=1}
          {/if}



          {if $gui->tlCanCreateIssue}
            {if $addBR}<br>{/if}
            {$addBR=1}
            {$args_labels.bug_create_into_bts}&nbsp;
            <input type="checkbox" name="createIssue" id="createIssue" onclick="javascript:toogleShowHide('issue_summary');
                         javascript:toogleRequiredOnShowHide('bug_summary');
                         javascript:toogleRequiredOnShowHide('artifactVersion');
                         javascript:toogleRequiredOnShowHide('artifactComponent');
                         javascript:populateBugDescription();">

            {* Fallback Integration Dropdown *}
            <div id="integration_dropdown_container" style="display:none !important; margin: 10px 0;">
              <div class="label">Select Integration:</div>
              <select id="integration_dropdown" name="integration_dropdown"
                onchange="handleIntegrationSelection(this.value)">
                <option value="">-- Select Integration --</option>
              </select>
            </div>
          {/if}



          {if $tlCfg->exec_cfg->copyLatestExecIssues->enabled}
            {if $addBR}<br>{/if}
            {$args_labels.bug_copy_from_latest_exec}&nbsp;
            <input type="checkbox" name="copyIssues[{$args_tcversion_id}]" id="copyIssues"
              {if $tlCfg->exec_cfg->copyLatestExecIssues->default} checked {/if}>
          {/if}


          <br />
          <br />
          <input type="hidden" name="selected_integration_id" id="selected_integration_id" value="">
          <input type="submit" name="save_results[{$args_tcversion_id}]" {$args_input_enable_mgmt}
            onclick="document.getElementById('save_button_clicked').value={$args_tcversion_id};doSubmitForHTML5();return checkSubmitForStatusCombo('statusSingle_{$args_tcversion_id}','{$ResultsStatusCode.not_run}')"
            value="{$args_labels.btn_save_tc_exec_results}" />

          <input type="submit" name="save_and_next[{$args_tcversion_id}]" {$args_input_enable_mgmt}
            onclick="document.getElementById('save_button_clicked').value={$args_tcversion_id};doSubmitForHTML5();return checkSubmitForStatusCombo('statusSingle_{$args_tcversion_id}','{$ResultsStatusCode.not_run}')"
            value="{$args_labels.btn_save_exec_and_movetonext}" />

          <input type="submit" name="move2next[{$args_tcversion_id}]" {$args_input_enable_mgmt}
            onclick="document.getElementById('save_button_clicked').value={$args_tcversion_id};"
            value="{$args_labels.btn_next}" />


        {else}
          <input type="submit" id="do_bulk_save" name="do_bulk_save" value="{$args_labels.btn_save_tc_exec_results}" />

        {/if}
      </div>
    </td>
  </tr>
  {if $args_save_type == 'bulk' && $args_execution_time_cfields != ''}
    <tr>
      <td colspan="2">
        <div id="cfields_exec_time_tcversionid_{$args_tcversion_id}" class="custom_field_container"
          style="background-color:#dddddd;">
          {$args_labels.testcase_customfields}
          {$args_execution_time_cfields.0} {* 0 => bulk *}
        </div>
      </td>
    </tr>
  {/if}
</table>

{if $gui->addIssueOp != '' && !is_null($gui->addIssueOp) && 
          !is_null($gui->addIssueOp.type) }
{$ak = $gui->addIssueOp.type}
<hr>
<table id="addIssueFeedback">
  <tr>
    <td colspan="2" class="label">{$args_labels.create_issue_feedback}</td>
  </tr>

  {if $ak == 'createIssue'}
    <tr>
      <td colspan="2">
        <div class="label">{$gui->addIssueOp[$ak].msg}</div>
      </td>
    </tr>
  {else}
    {foreach key=ik item=ikmsg from=$gui->addIssueOp[$ak]}
      <tr>
        <td colspan="2">
          <div class="label">{$ikmsg.msg}</div>
        </td>
      </tr>
    {/foreach}
  {/if}
</table>
<hr>
{/if}

<table style="display:none;" id="issue_summary">
  <tr>
    <td colspan="2">
      {* 
             IMPORTANT:
             Via Javascript the required attribute will be added when this input will be 
             done visible because user has clicked on 'Create Issue' checkbox
          *}
      <div class="label">{$args_labels.bug_summary}</div>
      <input type="text" id="bug_summary" name="bug_summary" value="{$gui->bug_summary}" size="{#BUGSUMMARY_SIZE#}"
        maxlength="{$gui->issueTrackerCfg->bugSummaryMaxLength}">
    </td>
  </tr>

  {$itMetaData = $gui->issueTrackerMetaData}
  {if '' != $itMetaData && null != $itMetaData}
    <tr>
      <td colspan="2">
        {include file="./issueTrackerMetadata.inc.tpl"
                   useOnSteps=0
          }
      </td>
    </tr>
  {/if}

  <tr>
    <td colspan="2">
      <div class="label">{$args_labels.bug_description}</div>
      <textarea id="bug_notes" name="bug_notes" rows="{#BUGNOTES_ROWS#}"
        cols="{$gui->issueTrackerCfg->bugSummaryMaxLength}"></textarea>
    </td>
  </tr>

  <tr>
    <td colspan="2">
      <input type="checkbox" name="addLinkToTL" id="addLinkToTL" {if $gui->addLinkToTLChecked} checked {/if}>
      <span class="label">{$args_labels.add_link_to_tlexec}</span>
    </td>
  </tr>

  <tr>
    <td colspan="2">
      <input type="checkbox" name="addLinkToTLPrintView" id="addLinkToTLPrintView"
        {if $gui->addLinkToTLPrintViewChecked} checked {/if}>
      <span class="label">{$args_labels.add_link_to_tlexec_print_view}</span>
    </td>
  </tr>

</table>

</br>
<div class="messages" style="align:center;">
  {$args_labels.exec_not_run_result_note}
</div>


<script>
  // Function to populate bug description with test case details
  function populateBugDescription() {
    console.log('populateBugDescription function called');

    // Only populate if checkbox is checked
    if (jQuery('#createIssue').is(':checked')) {
      console.log('Create Issue checkbox is checked');

      // Get test case details from the page
      var testCaseDetails = {};

      // Get test case ID and name
      console.log('Attempting to get test case title');
      var tcTitleElement = jQuery('.exec_tc_title').last();
      console.log('tcTitleElement found:', tcTitleElement.length > 0);
      var tcTitleText = tcTitleElement.text();
      console.log('tcTitleText:', tcTitleText);
      testCaseDetails.title = jQuery.trim(tcTitleText);

      // Get test case description
      console.log('Attempting to get test case description');
      var descElement = jQuery('.exec_test_spec');
      console.log('descElement found:', descElement.length > 0);
      testCaseDetails.description = descElement.text();

      // Get expected results
      console.log('Attempting to get expected results');
      var expectedResultsElement = jQuery('.exec_test_spec_title:contains("Expected Results")').next();
      console.log('expectedResultsElement found:', expectedResultsElement.length > 0);
      testCaseDetails.expected_results = expectedResultsElement.text();

      // Get actual results/notes
      console.log('Attempting to get notes');
      var notesElement = jQuery('#notes');
      console.log('notesElement found:', notesElement.length > 0);
      testCaseDetails.notes = notesElement.val();

      // Get execution status
      console.log('Attempting to get execution status');
      var statusSelect = jQuery('select[id^="statusSingle_"]');
      console.log('statusSelect found:', statusSelect.length > 0);
      var statusText = statusSelect.find('option:selected').text();
      console.log('statusText:', statusText);
      testCaseDetails.execution_status = statusText;

      // Get test execution path
      console.log('Attempting to get test execution path');
      var testSuiteTitle = jQuery('.exec_additional_info .exec_testsuite_details').text();
      console.log('testSuiteTitle:', testSuiteTitle);
      testCaseDetails.test_execution_path = jQuery.trim(testSuiteTitle);

      // Create formatted template
      console.log('Creating template with gathered data');
      var template = "";
      template += "=========================\nTest Details\n=========================\n";
      template += "Test Case: " + testCaseDetails.title + "\n";
      template += "Test Execution Path: " + testCaseDetails.test_execution_path + "\n\n";

      template += "=========================\nExpected Results\n=========================\n";
      template += testCaseDetails.expected_results + "\n\n";

      template += "=========================\nActual Results\n=========================\n";
      template += "Execution Status: " + testCaseDetails.execution_status + "\n";
      template += "Error Details: " + testCaseDetails.notes + "\n";

      console.log('Template created:', template);

      // Set the bug description field value
      console.log('Attempting to set bug_notes field');
      var bugNotesField = jQuery('#bug_notes');
      console.log('bugNotesField found:', bugNotesField.length > 0);
      bugNotesField.val(template);
      console.log('Template set to bug_notes field');
    } else {
      console.log('Create Issue checkbox is NOT checked');
    }
  }

  // Add event listener for the createIssue checkbox
  jQuery(document).on('change', '#createIssue', function() {
    console.log('createIssue checkbox changed, checked:', jQuery(this).is(':checked'));
    populateBugDescription();
  });

  jQuery(document).ready(function() {
    console.log('Document ready executed');

    // IMPORTANT
    // For some chosen select I want on page load to be DISPLAY NONE
    // That's why I've changes from original example on the line where styles were applied
    // 
    jQuery(".chosen-select-artifact").chosen({ width: "35%" });

    // From https://github.com/harvesthq/chosen/issues/515
    jQuery(".chosen-select-artifact").each(function() {
      // take each select and put it as a child of the chosen container
      // this mean it'll position any validation messages correctly
      jQuery(this).next(".chosen-container").prepend(jQuery(this).detach());

      // apply all the styles, personally, I've added this to my stylesheet
      // TESTLINK NOTE
      jQuery(this).attr("style", "display:none!important; position:absolute; clip:rect(0,0,0,0)");

      // to all of these events, trigger the chosen to open and receive focus
      jQuery(this).on("click focus keyup", function(event) {
        jQuery(this).closest(".chosen-container").trigger("mousedown.chosen");
      });
    });

    // Check if createIssue exists and log its state
    var createIssueCheckbox = jQuery('#createIssue');
    console.log('createIssue checkbox exists:', createIssueCheckbox.length > 0);
    if (createIssueCheckbox.length > 0) {
      console.log('createIssue checkbox initial state:', createIssueCheckbox.is(':checked'));
    }
  });
</script>