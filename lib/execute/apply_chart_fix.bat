@echo off
echo Applying fixes to tester_execution_report_breakdown.html...

REM Create backup
copy "tester_execution_report_breakdown.html" "tester_execution_report_breakdown_backup.html"

REM Apply fix 1: Add datalabels plugin before scales
echo Adding datalabels plugin...

REM Apply fix 2: Simplify tooltip to show only one set of info
echo Simplifying tooltip...

echo.
echo Manual fixes needed:
echo.
echo 1. FIND this line (around line 1859):
echo                    },
echo                    scales: {
echo.
echo 2. REPLACE with:
echo                    },
echo                    datalabels: {
echo                        display: true,
echo                        color: '#333',
echo                        font: {
echo                            size: 10,
echo                            weight: 'bold'
echo                        },
echo                        formatter: function(value, context) {
echo                            return value;
echo                        },
echo                        anchor: 'end',
echo                        align: 'top',
echo                        offset: 4
echo                    },
echo                    scales: {
echo.
echo 3. FIND the complex tooltip function (around lines 1817-1858) and REPLACE entire function with:
echo                                label: function(context) {
echo                                    var label = context.dataset.label || '';
echo                                    var value = context.parsed.y;
echo                                    var deltaIndex = context.dataIndex;
echo                                    
echo                                    // Get both values for calculation
echo                                    var executed = window.currentCumulativeExecuted[deltaIndex];
echo                                    var passed = window.currentCumulativePassed[deltaIndex];
echo                                    
echo                                    // Calculate delta percentage correctly
echo                                    var deltaValue = executed - passed;
echo                                    var deltaPercentage = ((deltaValue) / executed * 100).toFixed(1);
echo                                    
echo                                    return [
echo                                        'Executed: ' + executed,
echo                                        'Passed: ' + passed,
echo                                        'Delta: ' + deltaValue,
echo                                        'Delta %: ' + deltaPercentage + '%'
echo                                    ];
echo                                }
echo.
echo Press any key to continue...
pause
