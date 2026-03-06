// Simple test script to verify loading
alert('Redmine test script loaded successfully!');
console.log('Redmine test script loaded');

// Add a visible element to the page
window.addEventListener('DOMContentLoaded', function() {
    const testDiv = document.createElement('div');
    testDiv.style.padding = '10px';
    testDiv.style.margin = '10px';
    testDiv.style.backgroundColor = 'red';
    testDiv.style.color = 'white';
    testDiv.style.fontWeight = 'bold';
    testDiv.textContent = 'REDMINE TEST SCRIPT LOADED';
    document.body.insertBefore(testDiv, document.body.firstChild);
});
