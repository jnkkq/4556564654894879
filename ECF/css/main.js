document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const loginFormContainer = document.getElementById("login-form-container");
    const userInfo = document.getElementById("user-info");
    const userName = document.getElementById("user-name");
    const logoutButton = document.getElementById("logout-button");

    logoutButton.addEventListener("click", () => {
        fetch('logout.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                localStorage.removeItem("username");
                hideUserInfo();
                window.location.reload();
            }
        });
    });

    const displayUserInfo = (username) => {
        loginFormContainer.style.display = "none";
        userInfo.style.display = "flex";
        userName.textContent = username;
    };

    const hideUserInfo = () => {
        loginFormContainer.style.display = "flex";
        userInfo.style.display = "none";
    };

    // Check if user is already logged in
    const storedUsername = localStorage.getItem("username");
    if (storedUsername) {
        displayUserInfo(storedUsername);
    } else {
        hideUserInfo();
    }
});
