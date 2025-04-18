document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission
    
    const formData = new FormData(this); // Collect form data
    
    fetch('loginadmin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes("Invalid")) {
            document.querySelector('.error-message').textContent = data; // Display error
        } else {
            window.location.href = 'admin-dashboard.php'; // Redirect on success
        }
    })
    .catch(error => console.error('Error:', error));
});
