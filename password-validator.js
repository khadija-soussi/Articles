document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById("password");
    const passwordMessage = document.getElementById("password-message");
    const passwordHint = document.querySelector(".password-hint");

    passwordInput.addEventListener("input", function() {
        const password = passwordInput.value;
        let message = "";
        let isValid = false;

        if (password.length === 0) {
            passwordMessage.textContent = "";
            passwordMessage.className = "message";
            passwordHint.style.display = "block";
            return;
        }

        passwordHint.style.display = "none";

        if (password.length < 8) {
            message = "❌ At least 8 characters required";
        } else if (!/[A-Z]/.test(password) && !/[a-z]/.test(password)) {
            message = "❌ Include both uppercase and lowercase letters";
        } else if (!/[0-9]/.test(password)) {
            message = "❌ Include at least one number";
        } else if (!/[!@#$%^&*]/.test(password)) {
            message = "❌ Include at least one special character (!@#$%^&*)";
        } else {
            message = "✅ Strong password!";
            isValid = true;
        }

        passwordMessage.innerHTML = message;
        passwordMessage.className = isValid ? "message valid" : "message error";
        
        // Visual feedback on the input field
        passwordInput.style.borderColor = isValid ? "#28a745" : "#dc3545";
    });
});