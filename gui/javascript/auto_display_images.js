/**
 * Auto Display Images - TestLink Enhancement
 * 
 * This script automatically displays all attachment images inline when the page loads
 * without requiring users to click the eye icon for each image.
 */
document.addEventListener('DOMContentLoaded', function() {
  // Find all eye icons that are used to display images
  var eyeIcons = document.querySelectorAll('img[title="Display inline"]');
  
  // Click each eye icon to display the image
  for (var i = 0; i < eyeIcons.length; i++) {
    try {
      // Manually trigger the onclick event for each eye icon
      if (eyeIcons[i].onclick) {
        eyeIcons[i].onclick();
      }
    } catch (e) {
      console.log('Error clicking eye icon:', e);
    }
  }
});
