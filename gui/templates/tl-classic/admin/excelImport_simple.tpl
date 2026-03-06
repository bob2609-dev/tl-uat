{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource excelImport.tpl
Purpose: SmartY template - Excel test case import - Simplified version

*}
{include file="inc_head.tpl" openHead="yes" jsValidate="yes"}
{include file="inc_ext_js.tpl"}
{include file="bootstrap.inc.tpl"}

<script type="text/javascript">
{literal}
function validateForm() {
  var f = document.getElementById('import_form');
  if (f.uploadedFile.value == '') {
    alert('Please select an Excel file to upload');
    return false;
  }
  
  // Check test suite selection
  if (f.testsuiteID.value == '') {
    alert('Please select a Test Suite');
    return false;
  }
  
  return true;
}
{/literal}
</script>

</head>
<body>
<h1 class="title">{$gui->page_title|escape}</h1>

<div class="workBack">
  {if isset($gui->sqlExecutionResult)}
    <!-- SQL Execution Results -->
    {if $gui->sqlExecutionResult->status_ok}
      <div class="alert alert-success">
        <p><strong>SQL Execution Successful:</strong> {$gui->sqlExecutionResult->msg|escape}</p>
      </div>
    {else}
      <div class="alert alert-danger">
        <p><strong>SQL Execution Failed:</strong> {$gui->sqlExecutionResult->msg|escape}</p>
      </div>
    {/if}
  {/if}
  
  <!-- USING PROPER ISSET CHECKS TO AVOID OBJECT/ARRAY TYPE ERRORS -->
  <div>
    {if isset($gui->doImport)}
      {if isset($gui->import_status_ok) && $gui->import_status_ok}
        <div class="alert alert-success">
          {if isset($gui->importResults) && isset($gui->importResults->sql_file) && $gui->importResults->sql_file != ""}
            <hr>
            <h4>SQL File Generation Complete</h4>
            <p>SQL file has been generated successfully: <code>{$gui->importResults->sql_file|escape}</code></p>
            <p class="text-muted">SQL file generation completed at {$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}</p>
            
            <!-- Add link to SQL executor -->
            <div class="well">
              <h4>Execute SQL File</h4>
              <p>Click the button below to execute the generated SQL file:</p>
              <form action="lib/admin/pure_sql_executor.php" method="post">
                <input type="hidden" name="action" value="execute" />
                <input type="hidden" name="file" value="{$gui->importResults->sql_file|escape}" />
                <button type="submit" class="btn btn-success">Execute SQL File</button>
              </form>
            </div>
          {/if}
        </div>
      {else}
        {* Only show import failure if this is an actual import attempt (not initial page load) AND no SQL file was generated *}
        {if isset($gui->doImport) && $gui->doImport == true && !(isset($gui->importResults) && isset($gui->importResults->sql_file) && $gui->importResults->sql_file != "")}
          <div class="alert alert-danger">
            <p>Import failed. 
            {if isset($gui->import_msg)}
              {$gui->import_msg|escape}
            {/if}</p>
            
            {if isset($gui->file_check) && isset($gui->file_check->status_ok) && !$gui->file_check->status_ok}
              <p><strong>File check error:</strong> {$gui->file_check->msg|escape}</p>
            {/if}
          </div>
        {/if}
      {/if}
    {/if}
  </div>

  <form id="import_form" name="import_form" method="post" enctype="multipart/form-data" 
        action="lib/admin/excelImport.php" onsubmit="return validateForm();">
    <input type="hidden" name="doImport" value="1" />
    <!-- CSRF validation completely disabled for this flow -->

    <h2>Import Test Cases from Excel</h2>
    <p>This tool imports test cases from an Excel file into your TestLink database.</p>
    
    <div class="form-group">
      <label for="uploadedFile">Excel File (.xls, .xlsx):</label>
      <input type="file" name="uploadedFile" id="uploadedFile" class="form-control" />
    </div>
    
    <div class="form-group">
      <label for="sheetName">Sheet Name (Optional):</label>
      <input type="text" name="sheetName" id="sheetName" class="form-control" />
      <small class="form-text text-muted">Leave blank to use the first sheet</small>
    </div>
    
    <div class="form-group">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="inspectExcel" id="inspectExcel" value="1" /> 
          Inspect Excel before import (shows sheet names and structure)
        </label>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="cleanupExcel" id="cleanupExcel" value="1" /> 
          Clean up Excel file before import (fixes column names and removes empty rows)
        </label>
      </div>
    </div>
    
    <div class="form-group">
      <label for="databaseName">Target Database:</label>
      <select name="databaseName" id="databaseName" class="form-control" required>
        {if isset($gui->databases)}
          {foreach from=$gui->databases item=dbName}
            <option value="{$dbName|escape}" {if $dbName == $gui->currentDatabase}selected{/if}>{$dbName|escape}</option>
          {/foreach}
        {else}
          <option value="{$gui->currentDatabase|escape}" selected>{$gui->currentDatabase|escape}</option>
        {/if}
      </select>
      <small class="form-text text-muted">The database where SQL will be executed</small>
    </div>
    </div>
    
    <div class="form-group">
      <label for="testsuiteID">Test Suite ID:</label>
      <input type="text" name="testsuiteID" id="testsuiteID" class="form-control" required />
      <small class="form-text text-muted">Enter the numeric ID of the test suite where test cases will be imported</small>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Test Case Settings</div>
      <div class="panel-body">
        <div class="form-group">
          <label for="importStatus">Test Case Status:</label>
          <select name="importStatus" id="importStatus" class="form-control">
            {if isset($gui->testcaseStatus)}
              {foreach from=$gui->testcaseStatus key=id item=name}
                <option value="{$id}" {if $id==1}selected{/if}>{$name|escape}</option>
              {/foreach}
            {/if}
          </select>
        </div>
        
        <div class="form-group">
          <label for="authorID">Author ID:</label>
          <input type="number" name="authorID" id="authorID" class="form-control" value="{$smarty.session.userID}" required />
          <small class="form-text text-muted">Enter the numeric user ID of the author</small>
        </div>
        
        <div class="form-group">
          <label for="importance">Importance:</label>
          <select name="importance" id="importance" class="form-control">
            {if isset($gui->importanceOptions)}
              {foreach from=$gui->importanceOptions key=id item=name}
                <option value="{$id}" {if $id==2}selected{/if}>{$name|escape}</option>
              {/foreach}
            {/if}
          </select>
        </div>
        
        <div class="form-group">
          <label for="executionType">Execution Type:</label>
          <select name="executionType" id="executionType" class="form-control">
            {if isset($gui->executionOptions)}
              {foreach from=$gui->executionOptions key=id item=name}
                <option value="{$id}" {if $id==1}selected{/if}>{$name|escape}</option>
              {/foreach}
            {/if}
          </select>
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <input type="submit" name="import" value="Import Test Cases" class="btn btn-primary" />
    </div>
  </form>
</div>
</body>
</html>
