<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (!empty($error)): ?>
        <div style="color:red;"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>
        <button type="submit">Register</button>
    </form>
    <br>
    <a href="/login">Login</a>
</body>
</html> 