# Read the entire file content
$content = Get-Content -Path "lib\functions\testplan.class.php" -Raw

# Replace the helper_urgency_sql function
$oldFunction = @'
  function helper_urgency_sql\(\$filter\)
  \{
      
    \$cfg = config_get\("urgencyImportance"\);
    \$sql = '';
    if \(\$filter == HIGH\)
    \{
      \$sql \.= " AND \(urgency \* importance\) >= " \. \$cfg->threshold\['high'\];
    \}
    else if\(\$filter == LOW\)
    \{
      \$sql \.= " AND \(urgency \* importance\) < " \. \$cfg->threshold\['low'\];
    \}
    else
    \{
      \$sql \.= " AND \( \(\(urgency \* importance\) >= " \. \$cfg->threshold\['low'\] \. 
            " AND  \(\(urgency \* importance\) < " \. \$cfg->threshold\['high'\]\."\)\)\) ";
    \}    

    return \$sql;
  \}
'@

$newFunction = @'
  function helper_urgency_sql($filter)
  {
      
    $cfg = config_get("urgencyImportance");
    $sql = '';
    if ($filter == HIGH)
    {
      $sql .= " AND (TPTCV.urgency * TCV.importance) >= " . $cfg->threshold['high'];
    }
    else if($filter == LOW)
    {
      $sql .= " AND (TPTCV.urgency * TCV.importance) < " . $cfg->threshold['low'];
    }
    else
    {
      $sql .= " AND ( ((TPTCV.urgency * TCV.importance) >= " . $cfg->threshold['low'] . 
            " AND  ((TPTCV.urgency * TCV.importance) < " . $cfg->threshold['high']."))) ";
    }    

    return $sql;
  }
'@

# Replace the function
$content = $content -replace $oldFunction, $newFunction

# Write the modified content back to the file
$content | Set-Content -Path "lib\functions\testplan.class.php" -NoNewline

Write-Host "Successfully updated helper_urgency_sql function"
