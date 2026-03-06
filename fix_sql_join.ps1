# Add necessary table join to SQL query
$filePath = "lib\functions\testplan.class.php"
$content = Get-Content -Path $filePath -Raw

# Find the SQL query in getLinkedForExecTreeIVU method
$pattern = 'function getLinkedForExecTreeIVU\([^)]*\)\s*{([^}]*)}'

if ($content -match $pattern) {
    $functionBody = $matches[1]
    
    # Add join to tcversions table
    $newFunctionBody = $functionBody -replace 'JOIN \{\$this->tables\[''executions''\]} E', 
        "JOIN {$this->tables['tcversions']} TCV ON TPTCV.tcversion_id = TCV.id `n        JOIN {$this->tables['executions']} E"
    
    # Replace the function body
    $content = $content.Replace($functionBody, $newFunctionBody)
    
    # Write the modified content back to the file
    $content | Set-Content -Path $filePath -NoNewline
    
    Write-Host "Successfully added TCV table join to getLinkedForExecTreeIVU method"
} else {
    Write-Host "Could not find getLinkedForExecTreeIVU method"
}
