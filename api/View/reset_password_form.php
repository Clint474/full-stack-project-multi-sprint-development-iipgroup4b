<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../includes/style.css">

</head>
<body>

<?php include __DIR__ . "/../includes/nav.php"; ?>




<div class="container">
    <h2>Reset Password</h2>

    <form id="resetForm">
        <label>User Number:</label>
        <input type="number" id="userNr" required>

        <label>Old Password:</label>
        <input type="password" id="oldPassword" required>

        <label>New Password:</label>
        <input type="password" id="newPassword" required>

        <button type="submit">Reset Password</button>
    </form>

    <div id="message" class="message"></div>
</div>

<script>
document.getElementById("resetForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const messageDiv = document.getElementById("message");
    messageDiv.textContent = "";
    messageDiv.className = "message";

    const data = {
        userNr: document.getElementById("userNr").value,
        oldPassword: document.getElementById("oldPassword").value,
        newPassword: document.getElementById("newPassword").value
    };

    try {
        const response = await fetch("../auth/change_password.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            messageDiv.textContent = result.error;
            messageDiv.classList.add("error");
        } else {
            messageDiv.textContent = result.success;
            messageDiv.classList.add("success");
            document.getElementById("resetForm").reset();
        }

    } catch (error) {
        messageDiv.textContent = "Server error. Please try again.";
        messageDiv.classList.add("error");
    }
});
</script>

</body>
</html>
