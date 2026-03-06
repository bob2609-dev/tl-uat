{* Added Redmine Integration directly in the template *}
{literal}
<script type="text/javascript">
// Insert the script loading directly into the page
function loadRedmineIntegration() {
    // Create script elements
    var script1 = document.createElement('script');
    script1.src = '{/literal}{$basehref}{literal}redmine_inline_integration.js';
    script1.type = 'text/javascript';
    
    // Append to the document
    document.head.appendChild(script1);
    
    // Log to console
    console.log('Redmine integration scripts injected directly!');
}

// Call the function when the page loads
window.addEventListener('load', loadRedmineIntegration);
</script>
{/literal}
