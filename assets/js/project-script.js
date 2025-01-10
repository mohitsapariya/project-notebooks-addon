jQuery(document).ready(function($) {
    // When the AI label is clicked
    $('.keyinformation_ai').on('click', function(e) {
        e.preventDefault();

        // Get the content from the wp_editor
        var content = $('#keyinfo').val(); // Get the content in the editor

        // Check if content is available
        if(content.trim() !== '') {
            // Trigger AJAX call to the backend (WordPress)
            var data = {
                action: 'refine_and_reword_content', // The action hook for AJAX
                content: content, // The content from the editor
                nonce: customScriptVars.nonce // Optional: Use nonce for security
            };

            // Perform AJAX request
            $.post(customScriptVars.ajax_url, data, function(response) {
                // Update the editor with the response from the backend
                if(response.success) {
					console.log(response.data.refined_content);
                   var editorId = 'keyinfo'; // Replace with your editor's ID
					if (typeof tinyMCE !== 'undefined' && tinyMCE.get(editorId)) {
						tinyMCE.get(editorId).setContent(response.data.refined_content);  // Update content in the editor with specific ID
					} // Update wp_editor content
                } else {
                    //alert('Failed to refine content. Please try again.');
                }
            });
        } else {
            alert('Please enter some text to refine.');
        }
    });
});
