<script>
    (() => {
        // Function to insert Shiprocket button
        const insertShiprocketButton = () => {
            // Get our Shiprocket button HTML
            const shiprocketButton = document.getElementById('shiprocket-button-container');
            
            if (!shiprocketButton) return;
            
            // Find the order actions container in Bagisto's admin order view
            const orderActionsContainer = document.querySelector('.page-action');
            
            if (orderActionsContainer) {
                // Move our button into the actions container
                orderActionsContainer.appendChild(shiprocketButton.content.cloneNode(true));
                
                // Remove the template element after we've used it
                shiprocketButton.remove();
            }
        };
        
        // Run on page load
        document.addEventListener('DOMContentLoaded', insertShiprocketButton);
    })();
</script>