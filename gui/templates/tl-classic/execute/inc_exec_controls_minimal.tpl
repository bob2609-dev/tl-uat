{* Minimal test version of inc_exec_controls.tpl *}

{if $gui->tlCanCreateIssue}
  <br />
  {$args_labels.bug_create_into_bts}&nbsp;
  <input type="checkbox" name="createIssue"  id="createIssue" 
         onclick="javascript:toogleShowHide('issue_summary');
         javascript:toogleRequiredOnShowHide('bug_summary');
         javascript:toogleRequiredOnShowHide('artifactVersion');
         javascript:toogleRequiredOnShowHide('artifactComponent');
         javascript:populateBugDescription();" />

  {* Integration Dropdown *}
  <div id="integration_dropdown_container" style="display:none !important; margin: 10px 0;">
    <div class="label">Select Integration:</div>
    <select id="integration_dropdown" name="integration_dropdown" onchange="handleIntegrationSelection(this.value)">
      <option value="">-- Select Integration --</option>
    </select>
  </div>
{/if}
