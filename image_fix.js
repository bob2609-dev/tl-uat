/**
 * TestLink Image Display Fix
 * This script helps fix image display issues by redirecting image requests
 * to our custom image handler scripts.
 */

// Fix images immediately - don't wait for DOMContentLoaded
(function() {
    // Try to run immediately
    fixAllImages();
    
    // Also run when DOM is fully loaded to catch any images loaded later
    document.addEventListener('DOMContentLoaded', function() {
        // Fix all images in the page
        fixAllImages();
        
        // Create a mutation observer to handle dynamically added images
        createImageObserver();
    });
    
    // Also run on window load to catch images that might be added dynamically
    window.addEventListener('load', function() {
        fixAllImages();
    });
})();

/**
 * Fix all images in the current page
 */
function fixAllImages() {
    // Find all image elements
    var images = document.querySelectorAll('img');
    
    // Process each image
    images.forEach(function(img) {
        fixImage(img);
    });
}

/**
 * Fix a single image
 * @param {HTMLImageElement} img - The image element to fix
 */
function fixImage(img) {
    var src = img.getAttribute('src');
    
    // Only process TestLink attachment images
    if (src && src.includes('attachmentdownload.php')) {
        // Extract the attachment ID
        var match = src.match(/[?&]id=(\d+)/);
        if (match && match[1]) {
            var id = match[1];
            
            // Create a new URL pointing to our fixed handler
            var newSrc = 'attachment_fixed.php?id=' + id;
            
            // Update the image source
            img.setAttribute('src', newSrc);
            
            // Add click handler for lightbox effect
            img.style.cursor = 'pointer';
            img.setAttribute('data-original-id', id);
            
            img.addEventListener('click', function() {
                var attachmentId = this.getAttribute('data-original-id');
                window.open('image_proxy.php?id=' + attachmentId, '_blank');
            });
            
            // Force a reload of the image
            img.setAttribute('src', newSrc + '&t=' + new Date().getTime());
            
            console.log('Fixed image with ID: ' + id);
        }
    }
}

/**
 * Create a mutation observer to handle dynamically added images
 */
function createImageObserver() {
    // Create a new observer
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            // Check for added nodes
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    // If the added node is an image, fix it
                    if (node.tagName === 'IMG') {
                        fixImage(node);
                    }
                    
                    // If it's an element that might contain images, find and fix them
                    if (node.nodeType === 1) { // ELEMENT_NODE
                        var images = node.querySelectorAll('img');
                        images.forEach(function(img) {
                            fixImage(img);
                        });
                    }
                });
            }
        });
    });
    
    // Start observing the entire document
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
