<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Password reset</title>
</head>
<body>
    <h3>Hello Admin</h3>
    <h4>User {{ isset($username) ? $username : "" }} password has been changed, new password is {{ isset($password_text) ? $password_text : "" }}</h4>
</body>
</html>