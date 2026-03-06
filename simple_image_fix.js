/**
 * Ultra-simple image fix for TestLink
 */

// Run immediately when script loads
(function() {
    // Fix all images immediately 
    fixImages();
    
    // Also run on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', fixImages);
    
    // And run on load
    window.addEventListener('load', fixImages);
    
    // Run periodically to catch any dynamically loaded images
    setInterval(fixImages, 1000);
})();

// Simple function to fix all image tags
function fixImages() {
    // Find all images
    var images = document.querySelectorAll('img');
    
    // Loop through each image
    for (var i = 0; i < images.length; i++) {
        var img = images[i];
        var src = img.getAttribute('src');
        
        // Only fix TestLink attachments
        if (src && src.indexOf('attachmentdownload.php') !== -1) {
            // Extract the ID
            var matches = src.match(/[?&]id=(\d+)/);
            if (matches && matches[1]) {
                var id = matches[1];
                
                // Replace with our direct image script
                var newSrc = 'direct_image.php?id=' + id + '&t=' + new Date().getTime();
                
                // Only change if needed
                if (img.getAttribute('src') !== newSrc) {
                    img.setAttribute('src', newSrc);
                    console.log('Fixed image with ID: ' + id);
                }
            }
        }
    }
}
