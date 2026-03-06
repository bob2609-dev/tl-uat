/**
 * Standalone Execution Integration
 * This script modifies the TestLink navigation tree links to redirect to our standalone execution page
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Standalone integration loaded');
    
    // Initialize after a short delay to ensure the tree is loaded
    setTimeout(modifyNavigationTree, 1000);
    
    // Also set up a mutation observer to catch dynamically added elements
    setupMutationObserver();
});

/**
 * Set up a mutation observer to watch for changes to the DOM
 * This will help catch dynamically loaded tree nodes
 */
function setupMutationObserver() {
    // Target element - the main content area where the tree is loaded
    const targetNode = document.getElementById('treeCluetip') || document.body;
    
    // Observer configuration
    const config = { childList: true, subtree: true };
    
    // Callback function to execute when mutations are observed
    const callback = function(mutationsList, observer) {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                modifyNavigationTree();
            }
        }
    };
    
    // Create an observer instance linked to the callback function
    const observer = new MutationObserver(callback);
    
    // Start observing the target node for configured mutations
    observer.observe(targetNode, config);
    
    console.log('Mutation observer set up');
}

/**
 * Modify the navigation tree links to redirect to our standalone execution page
 */
function modifyNavigationTree() {
    console.log('Modifying navigation tree links');
    
    // Find all links in the tree
    const links = document.querySelectorAll('a[href*="lib/execute/execSetResults.php"]');
    
    links.forEach(function(link) {
        // Original href
        const originalHref = link.getAttribute('href');
        console.log('Found execution link:', originalHref);
        
        // Extract parameters from the original URL
        const urlParams = new URLSearchParams(originalHref.split('?')[1]);
        
        // Get required parameters
        const id = urlParams.get('id');
        const version_id = urlParams.get('version_id') || urlParams.get('tcversion_id');
        const tplan_id = urlParams.get('tplan_id');
        const build_id = urlParams.get('build_id');
        
        // Create new URL to standalone execution page
        const newHref = 'standalone_execution.php?id=' + id + 
                       '&version_id=' + version_id + 
                       '&tplan_id=' + tplan_id + 
                       '&build_id=' + (build_id || '');
        
        // Update the link
        link.setAttribute('href', newHref);
        link.setAttribute('data-original-href', originalHref); // Store original for reference
        link.setAttribute('title', 'Execute in PHP 8 compatible mode');
        
        // Visually indicate modified links
        link.style.color = '#0066cc';
        
        console.log('Modified link to:', newHref);
    });
}

/**
 * Periodically check for new links that might have been added dynamically
 */
setInterval(modifyNavigationTree, 5000); // Check every 5 seconds
