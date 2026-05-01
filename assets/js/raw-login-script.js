const form = document.getElementById("loginForm");
const button = form.querySelector(".btn-login");
const messageBox = document.getElementById("message");
const messageIcon = messageBox.querySelector("i");
const messageText = messageBox.querySelector(".message-text");

form.addEventListener("submit", function (e) {
    e.preventDefault();

    // UI state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';

    // Hide message box
    messageBox.classList.remove("show", "success", "error");

    const formData = new FormData(form);

    fetch("/auth/login-process.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        // Update message content
        messageText.textContent = data.message;

        if (data.status === "success") {
            messageBox.className = "message success show";
            messageIcon.className = "fas fa-check-circle";
        
            setTimeout(() => {
                window.location.href = "/dashboard";
            }, 700);
        
        } else if (data.status === "blocked") {
            messageBox.className = "message error show";
            messageIcon.className = "fas fa-exclamation-circle";
        
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-lock"></i> Blocked';
        
        } else {
            messageBox.className = "message error show";
            messageIcon.className = "fas fa-exclamation-circle";
        
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
        }

    })
    .catch(() => {
        // Set error styling for network error
        messageBox.className = "message error show";
        messageIcon.className = "fas fa-exclamation-circle";
        messageText.textContent = "An error occurred. Please try again.";
        
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
    });
});