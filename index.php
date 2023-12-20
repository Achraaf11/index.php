</head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width
initial-scale=1.0">



<?php
include('config.php');
// Set the default timezone to Tunis
date_default_timezone_set('Africa/Tunis');

$error_message = ""; // إضافة هذا المتغير

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mac_address = md5($user_agent);
    // Proceed with user registration
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Additional information
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if passwords match and meet the length requirement
    if ($password !== $confirm_password || strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error_message = "خطأ: كلمة المرور يجب أن تتكون من 6 أحرف أو أكثر وتحتوي على أحرف وأرقام!";
    } else {
        // Check if the username meets the length requirement
        if (strlen($username) < 4) {
            $error_message = "خطأ: اسم المستخدم يجب أن يتكون من 4 أحرف أو أكثر!";
        } else {
            // Check for duplicate usernames
            $check_query = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($check_query);
            if ($result->num_rows > 0) {
                $error_message = "خطأ: اسم المستخدم موجود بالفعل!";
            } else {
                // Generate a unique code (16 characters of uppercase letters and numbers)
                $unique_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
                // Get current date and time in Tunis timezone
                $current_datetime = new DateTime('now', new DateTimeZone('Africa/Tunis'));
                $formatted_datetime = $current_datetime->format('Y-m-d H:i:s');
                // Insert user data, including the unique code, time, into the database
                $insert_query = "INSERT INTO users (username, password, user_agent, ip_address, mac_address, unique_code, time) VALUES ('$username', '$password', '$user_agent', '$ip_address', '$mac_address', '$unique_code', '$formatted_datetime')";
                // Check if the query is executed successfully
                if ($conn->query($insert_query) === TRUE) {
                    // Redirect to login page on successful registration
                    header("Location: login.php");
                } else {
                    $error_message = "خطأ: " . $insert_query . "<br>" . $conn->error;
                }
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
</head>

<body>
    <br><br>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
       <h2>Sign Up</h2>
        <?php
        if (!empty($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
  <!-- Your existing form elements -->
<label for="username">username :</label>
<input type="text" name="username" id="username" required><br>
<label for="password">Password :</label>
<input type="password" name="password" id="password" required><br>
<label for="confirm_password">Confirm Password :</label>
<input type="password" name="confirm_password" id="confirm_password" required><br>
<input type="submit" value="Sign Up">
<p> Already have account? <a class="signup-link" href="login.php">  Sign In  </a></p>
</form>

</html>




<style>
h2 {
text-align: center;
color: #ffffff ;
}
p {
text-align: center;
color: #ffffff ;
}
form {
background-color: rgba(200, 200, 200, 0.2); /* تعديل درجة الشفافية هنا */
padding: 20px;
border-radius: 5px;
max-width: 200px;
margin: 50px auto;
padding: 20px;
}
label {
display: block;
margin-bottom: 8px;
color: #ffffff ;
}
input {
width: 100%;
padding: 8px;
margin-bottom: 15px;
box-sizing: border-box;
border: 1px solid #ccc;
border-radius: 4px;
}
input[type="submit"] {
background-color: #3498db;
color: #fff;
cursor: pointer;
}
input[type="submit"]:hover {
background-color: #2980b9;
}
</style>
<?php include('in/back-ground.php'); ?>