# Execution Counter Optimization Implementation Plan

Based on the detailed analysis in suggestions.md, here's the step-by-step implementation:

## Phase 1: Configuration Setup
- [ ] Add global variable to custom_config.inc.php
- [ ] Set default value to false (backwards compatibility)

## Phase 2: execTreeMenu.inc.php Modifications
- [ ] Import global variable in execTree function
- [ ] Import global variable in testPlanTree function  
- [ ] Wrap prepareExecTreeNode call with conditional
- [ ] Add fallback counter initialization
- [ ] Override render options to respect global flag

## Phase 3: treeMenu.inc.php Modifications
- [ ] Add conditional check in renderExecTreeNode
- [ ] Add early return in create_counters_info function

## Phase 4: Testing & Validation
- [ ] Test with counters enabled (default)
- [ ] Test with counters disabled (performance mode)
- [ ] Verify filter functionality still works
- [ ] Check "Hide Test Cases" option behavior

## Key Considerations
- The optimization targets PHP recursion processing, not SQL queries
- Initial SQL query still runs to get execution status for coloring
- Some filter behaviors may change when counters are disabled
- Memory usage from 90k test cases still needs consideration

## Expected Performance Impact
- PHP CPU usage: 60-80% reduction
- Tree generation time: Significant improvement
- Memory usage: Same (SQL still loads 90k rows)
- Visual impact: No status breakdown in tree labels
