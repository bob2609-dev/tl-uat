# PowerShell script to ensure urgency condition is properly applied in both parts of the SQL query
$filePath = "lib\functions\testplan.class.php"

# Read the file content
$content = Get-Content -Path $filePath -Raw

# Find the getLinkedForExecTreeIVU method and add the urgency condition to the not_run part as well
# The key issue is that the SQL condition using TCV.importance is in $my['where']['where'] but not in $my['where']['not_run']
$replacementPattern = @'
    if (!is_null($ic['filters']['urgencyImportance'])) {
      $ic['where']['where'] .= $this->helper_urgency_sql($ic['filters']['urgencyImportance']);
'@

$replacementContent = @'
    if (!is_null($ic['filters']['urgencyImportance'])) {
      // Add urgency condition to both where clauses to ensure consistency in UNION queries
      $urgencyCondition = $this->helper_urgency_sql($ic['filters']['urgencyImportance']);
      $ic['where']['where'] .= $urgencyCondition;
      $ic['where']['not_run'] .= $urgencyCondition; 
'@

# Replace the content
$updatedContent = $content -replace [regex]::Escape($replacementPattern), $replacementContent

# Write the modified content back to the file
$updatedContent | Set-Content -Path $filePath -Encoding UTF8

Write-Host "Updated initGetLinkedForTree method to add urgency condition to both where clauses"
