<?php
/**
 * AJAX content include file for test execution summary
 * Displays hierarchical execution data
 */

// Extract data for display in AJAX response
?>
<div class="hierarchical-data">
    <div class="project-section">
        <div class="project-header" onclick="toggleDetails('project_<?php echo $projectId; ?>')">
            <span class="toggle-icon" id="icon_project_<?php echo $projectId; ?>">▼</span>
            <span class="project_name"><?php echo htmlspecialchars($project['name']); ?></span>
        </div>
        
        <div class="project-content" id="project_<?php echo $projectId; ?>">
            <?php foreach ($project['testplans'] as $testplanId => $testplan): ?>
                <div class="testplan-section">
                    <div class="testplan-header" onclick="toggleDetails('testplan_<?php echo $testplanId; ?>')">
                        <span class="toggle-icon" id="icon_testplan_<?php echo $testplanId; ?>">▼</span>
                        <span class="plan_name"><?php echo htmlspecialchars($testplan['name']); ?></span>
                    </div>
                    
                    <div class="testplan-content" id="testplan_<?php echo $testplanId; ?>">
                        <?php foreach ($testplan['suites'] as $suiteId => $suite): ?>
                            <div class="suite-section">
                                <div class="suite-header" onclick="toggleDetails('suite_<?php echo $suiteId; ?>')">
                                    <span class="toggle-icon" id="icon_suite_<?php echo $suiteId; ?>">▼</span>
                                    <span class="suite_name"><?php echo htmlspecialchars($suite['name']); ?></span>
                                    <?php if (isset($suiteCounts[$suiteId])): ?>
                                    <span class="suite_count">
                                        <?php echo $suiteCounts[$suiteId]['count']; ?> tests
                                        (<?php echo $suiteCounts[$suiteId]['statuses']['p']; ?> passed,
                                        <?php echo $suiteCounts[$suiteId]['statuses']['f']; ?> failed)
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="suite-content" id="suite_<?php echo $suiteId; ?>">
                                    <?php if (!empty($suite['executions'])): ?>
                                    <div class="executions">
                                        <table class="executions_table">
                                            <tr>
                                                <th>Test Case</th>
                                                <th>Status</th>
                                                <th>Build</th>
                                                <th>Tester</th>
                                                <th>Timestamp</th>
                                            </tr>
                                            <?php foreach ($suite['executions'] as $execution): ?>
                                                <tr class="status_<?php echo $execution['execution_status']; ?>">
                                                    <td><?php echo htmlspecialchars($execution['tc_name']); ?></td>
                                                    <td class="status_cell">
                                                        <span class="<?php echo $statusLabels[$execution['execution_status']]; ?>">
                                                            <?php echo $statusLabels[$execution['execution_status']]; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($execution['build_name']); ?></td>
                                                    <td>
                                                        <?php if (!empty($execution['tester_firstname']) && !empty($execution['tester_lastname'])): ?>
                                                            <?php echo htmlspecialchars($execution['tester_firstname'] . ' ' . $execution['tester_lastname']); ?>
                                                        <?php elseif (!empty($execution['tester_login'])): ?>
                                                            <?php echo htmlspecialchars($execution['tester_login']); ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($execution['execution_timestamp'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
