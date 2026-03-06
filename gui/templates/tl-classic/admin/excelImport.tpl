{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource excelImport.tpl
Purpose: SmartY template - Excel test case import

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
      {if !empty($gui->sqlExecutionResult->details)}
        <div class="panel panel-default">
          <div class="panel-heading">SQL Execution Details</div>
          <div class="panel-body">
            <pre style="max-height: 200px; overflow-y: auto;">
{foreach from=$gui->sqlExecutionResult->details item=detail}
{$detail|escape}
{/foreach}
            </pre>
          </div>
        </div>
      {/if}
    {else}
      <div class="alert alert-danger">
        <p><strong>SQL Execution Failed:</strong> {$gui->sqlExecutionResult->msg|escape}</p>
        {if !empty($gui->sqlExecutionResult->details)}
          <div class="panel panel-default mt-3">
            <div class="panel-heading">Error Details</div>
            <div class="panel-body">
              <pre style="max-height: 200px; overflow-y: auto;">
{foreach from=$gui->sqlExecutionResult->details item=detail}
{$detail|escape}
{/foreach}
              </pre>
            </div>
          </div>
        {/if}
      </div>
    {/if}
  {/if}
  
  {if isset($gui->file_check) && $gui->file_check.status_ok && isset($gui->importResults) && isset($gui->importResults->status_ok) && $gui->importResults->status_ok}
    <div class="alert alert-success">
      <p>{$gui->importResults->msg|escape}</p>
    </div>
    
    <div class="well">
      <h4>Generated SQL File</h4>
      <p>File: <strong>{$gui->importResults->sql_file|escape}</strong></p>
      <p>Path: <code>{$gui->importResults->sql_file_path|escape}</code></p>
      <p>Number of test cases imported: <strong>{$gui->importResults->num_imported}</strong></p>
      
      <div class="panel panel-default">
        <div class="panel-heading">SQL Content</div>
        <div class="panel-body">
          <pre style="max-height: 400px; overflow-y: auto;">{$gui->importResults->sql_content|escape}</pre>
        </div>
      </div>
      
      <p>
        <a class="btn btn-sm btn-primary" href="lib/execute/downloadExecResult.php?file={$gui->importResults->sql_file|escape}">
          <span class="fa fa-download"></span> Download SQL File
        </a>
        
        <!-- Link to pure_sql_executor.php instead of direct execution -->
        <a class="btn btn-sm btn-warning" href="lib/admin/pure_sql_executor.php?action=view&file={$gui->importResults->sql_file|escape}" style="margin-left: 10px;">
          <span class="fa fa-play"></span> Execute SQL with SQL Executor
        </a>
      </p>
      
      <!-- SQL Executor Description -->
      <div class="alert alert-info" style="margin-top: 15px;">
        <h4>SQL Execution</h4>
        <p><strong>Note:</strong> Click the button above to open the SQL Executor tool where you can review and execute the SQL script.</p>
        <p>The SQL Executor provides a safer environment for executing SQL with better error handling and detailed feedback.</p>
      </div>
    </div>
    
  {elseif $gui->doImport}
    <div class="alert alert-danger">
      {if isset($gui->importResults) && isset($gui->importResults->msg)}
        <p>{$gui->importResults->msg|escape}</p>
      {else}
        <p>{$gui->file_check.msg|escape}</p>
      {/if}
    </div>
  {/if}



  <form id="import_form" name="import_form" method="post" enctype="multipart/form-data" 
        action="lib/admin/excelImport.php" onsubmit="return validateForm();">
    <input type="hidden" name="doImport" value="1" />
    <!-- Using Smarty's automatic CSRF protection -->

    <h2>Import Test Cases from Excel</h2>
    <p>This tool imports test cases from an Excel file into your TestLink database.</p>
    
    <div class="form-group">
      <label for="uploadedFile">Excel File (.xls, .xlsx):</label>
      <input type="file" name="uploadedFile" id="uploadedFile" class="form-control" />
    </div>
    
    <div class="form-group">
      <label for="sheetName">Sheet Name (Optional):</label>
      <input type="text" name="sheetName" id="sheetName" class="form-control" placeholder="Leave empty to use first sheet" />
      <small class="form-text text-muted">Specify the name of the Excel sheet to import from. Leave empty to use the first sheet.</small>
    </div>
    
    <div class="form-group">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="inspectExcel" id="inspectExcel" value="1" /> 
          Inspect Excel file before import (validates file structure)
        </label>
      </div>
    </div>
    
    <div class="form-group">
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
        {if $gui->databases}
          {foreach from=$gui->databases item=dbName}
            <option value="{$dbName}" {if $dbName==$gui->currentDatabase}selected{/if}>{$dbName|escape}</option>
          {/foreach}
        {else}
          <option value="{$gui->currentDatabase}">{$gui->currentDatabase|escape}</option>
        {/if}
      </select>
      <small class="form-text text-muted">Database where the SQL will be executed</small>
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
            {foreach from=$gui->testcaseStatus key=id item=name}
              <option value="{$id}" {if $id==1}selected{/if}>{$name|escape}</option>
            {/foreach}
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
            {foreach from=$gui->importanceOptions key=id item=name}
              <option value="{$id}" {if $id==2}selected{/if}>{$name|escape}</option>
            {/foreach}
          </select>
        </div>
        
        <div class="form-group">
          <label for="executionType">Execution Type:</label>
          <select name="executionType" id="executionType" class="form-control">
            {foreach from=$gui->executionOptions key=id item=name}
              <option value="{$id}" {if $id==1}selected{/if}>{$name|escape}</option>
            {/foreach}
          </select>
        </div>
        
        <div class="form-group">
          <label for="nodeOrder">Starting Node Order:</label>
          <input type="number" name="nodeOrder" id="nodeOrder" class="form-control" value="1020" min="1" />
        </div>
      </div>
    </div>
    
    <div class="panel panel-default">
      <div class="panel-heading">Excel File Format Information</div>
      <div class="panel-body">
        <p>Your Excel file should contain the following columns:</p>
        <ul>
          <li><strong>Test Case ID</strong> - Unique identifier for the test case</li>
          <li><strong>Test Case Summary</strong> - Title or summary of the test case</li>
          <li><strong>Test Case Description</strong> - Detailed description</li>
          <li><strong>Test Type</strong> - Type of test (e.g., Functional, Performance)</li>
          <li><strong>Test Script</strong> - Test steps</li>
          <li><strong>Execution Path</strong> - Path to execute the test</li>
          <li><strong>Expected Results</strong> - Expected outcome</li>
          <li><strong>ER Process</strong> - Expected result process</li>
        </ul>
      </div>
    </div>
    
    <div class="form-group">
      <input type="submit" name="import" value="Import Test Cases" class="btn btn-primary" />
    </div>
  </form>
  
  {if $gui->import_result}
    <hr/>
    <div class="well">
      {if $gui->import_result->status_ok}
        <div class="alert alert-success">{$gui->import_result->msg|escape}</div>
        
        <h4>Generated SQL File</h4>
        <p>File: <strong>{$gui->import_result->sql_file|escape}</strong></p>
        <p>Path: <code>{$gui->import_result->sql_file_path|escape}</code></p>
        
        <div class="panel panel-default">
          <div class="panel-heading">SQL Content</div>
          <div class="panel-body">
            <pre style="max-height:400px;overflow-y:auto;">{$gui->import_result->sql_content|escape}</pre>
          </div>
        </div>
        
        <p>
          <a class="btn btn-sm btn-primary" href="lib/execute/downloadExecResult.php?file={$gui->import_result->sql_file|escape}">
            <span class="fa fa-download"></span> Download SQL File
          </a>
        </p>
      {else}
        <div class="alert alert-danger">{$gui->import_result->msg|escape}</div>
      {/if}
    </div>
  {/if}
</div>
</body>
</html>
