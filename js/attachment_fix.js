/**
 * Fix for TestLink attachments
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix for "attachment disabled" placeholder
    var disabledAttachments = document.querySelectorAll('.attachment-container, td:contains("attachment disabled")');
    if (disabledAttachments.length > 0) {
        // Find all attachment references in the database
        fetchAttachments();
    }
    
    // Fix attachment download links
    fixAttachmentLinks();
    
    // Override toogleImageURL function if it exists
    if (typeof window.toogleImageURL === 'function') {
        var originalToogleImageURL = window.toogleImageURL;
        window.toogleImageURL = function(id, attachmentID) {
            return '<img src="/attachment_download_fixed.php?id=' + attachmentID + '" style="max-width:800px;">';
        };
    }
});

function fixAttachmentLinks() {
    // Find all attachment download links
    var links = document.querySelectorAll('a[href*="attachmentdownload.php"]');
    
    // Replace them with our custom script
    for (var i = 0; i < links.length; i++) {
        var link = links[i];
        var href = link.getAttribute('href');
        
        // Extract the attachment ID
        var match = href.match(/id=(\d+)/);
        if (match && match[1]) {
            var id = match[1];
            var newUrl = '/attachment_download_fixed.php?id=' + id;
            link.setAttribute('href', newUrl);
            
            // Also fix any related inline display functionality
            var eyeIcon = link.nextElementSibling;
            if (eyeIcon && eyeIcon.hasAttribute('onclick') && 
                eyeIcon.getAttribute('onclick').indexOf('toogleImageURL') !== -1) {
                var oldOnClick = eyeIcon.getAttribute('onclick');
                var newOnClick = oldOnClick.replace('toogleImageURL', 'fixedToogleImageURL');
                eyeIcon.setAttribute('onclick', newOnClick);
            }
        }
    }
}

function fetchAttachments() {
    // This function would normally make an AJAX call to get attachments
    // For now, we'll just look for the execution ID in the URL
    var match = window.location.href.match(/[\?&]id=(\d+)/);
    if (match && match[1]) {
        var execId = match[1];
        
        // Find disabled attachment areas
        var disabledElements = document.querySelectorAll('td:contains("attachment disabled")');
        for (var i = 0; i < disabledElements.length; i++) {
            var element = disabledElements[i];
            
            // Replace the "attachment disabled" text with our fixed attachments
            element.innerHTML = '<div id="fixed-attachments-container">Loading attachments...</div>';
            
            // In a real implementation, you would make an AJAX call here to get attachment info
            // For now, we'll simulate it
            fetchAttachmentsByExecution(execId, 'fixed-attachments-container');
        }
    }
}

function fetchAttachmentsByExecution(execId, containerId) {
    // In a real implementation, this would be an AJAX call
    // For now, we'll set a timeout to simulate it
    setTimeout(function() {
        var container = document.getElementById(containerId);
        if (container) {
            // Create an iframe that loads our attachment list script
            var iframe = document.createElement('iframe');
            iframe.src = '/attachment_list.php?exec_id=' + execId;
            iframe.style.width = '100%';
            iframe.style.height = '150px';
            iframe.style.border = 'none';
            container.innerHTML = '';
            container.appendChild(iframe);
        }
    }, 500);
}

// Helper function for finding elements containing text
Element.prototype.contains = function(text) {
    return (this.textContent || this.innerText).indexOf(text) > -1;
};

// Fixed version of toogleImageURL
function fixedToogleImageURL(containerId, attachmentId) {
    var container = document.getElementById(containerId);
    if (container) {
        if (container.innerHTML === '') {
            container.innerHTML = '<img src="/attachment_download_fixed.php?id=' + attachmentId + '" style="max-width:800px;">';
        } else {
            container.innerHTML = '';
        }
    }
    return false;
}