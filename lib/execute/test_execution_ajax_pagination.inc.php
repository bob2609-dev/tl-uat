<?php
/**
 * AJAX pagination include file for test execution summary
 * Displays pagination controls
 */
?>
<!-- Pagination Controls for AJAX -->
<div class="pagination">
    <div class="pagination-controls">
        <?php if ($gui->page > 1): ?>
            <a href="javascript:void(0);" data-page="1" class="page-link">«</a>
            <a href="javascript:void(0);" data-page="<?php echo $gui->page-1; ?>" class="page-link">‹</a>
        <?php endif; ?>
        
        <?php
        // Show page numbers with a limit of 5 links
        $startPage = max(1, $gui->page-2);
        $endPage = min($gui->totalPages, $startPage+4);
        
        if ($startPage > 1):
        ?>
            <span class="page-ellipsis">...</span>
        <?php endif; ?>
        
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <?php if ($i == $gui->page): ?>
                <span class="page-link active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="javascript:void(0);" data-page="<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($endPage < $gui->totalPages): ?>
            <span class="page-ellipsis">...</span>
        <?php endif; ?>
        
        <?php if ($gui->page < $gui->totalPages): ?>
            <a href="javascript:void(0);" data-page="<?php echo $gui->page+1; ?>" class="page-link">›</a>
            <a href="javascript:void(0);" data-page="<?php echo $gui->totalPages; ?>" class="page-link">»</a>
        <?php endif; ?>
    </div>
    
    <div class="pagination-info">
        <?php 
        $pageLabel = isset($labels['page']) ? $labels['page'] : 'Page';
        $ofLabel = isset($labels['of']) ? $labels['of'] : 'of';
        $executionsLabel = isset($labels['executions']) ? $labels['executions'] : 'executions';
        
        echo "$pageLabel {$gui->page} $ofLabel {$gui->totalPages} ({$gui->totalExecutionsCount} $executionsLabel)";
        ?>
    </div>
</div>
