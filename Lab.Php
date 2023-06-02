<?php
$errors = [];
$full_name = '';
$email = '';
$gender = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];

    // Validate Full Name
    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }

    // Validate Email Address
    if (empty($email)) {
        $errors[] = "Email Address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Address.";
    }

    // Connect to MySQL database
    $host = 'localhost';
    $db = 'your_database_name';
    $user = 'your_username';
    $password = 'your_password';

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    // Insert data into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO students (full_name, email, gender) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$full_name, $email, $gender]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Error inserting data into the database.";
        }
    }

    // Close the database connection
    $pdo = null;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Registration Form</title>
</head>
<body>
  <h1>Student Registration Form</h1>
  <?php if (isset($success)): ?>
    <p>Registration successful!</p>
  <?php else: ?>
    <?php if (!empty($errors)): ?>
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo $error; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" action="submit.php">
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
      <br>
      <label for="email">Email Address:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
      <br>
      <label for="gender">Gender:</label>
      <select id="gender" name="gender" required>
        <option value="">Select</option>
        <option value="male" <?php if ($gender === 'male') echo 'selected'; ?>>Male</option>
        <option value="female" <?php if ($gender === 'female') echo 'selected'; ?>>Female</option>
        <option value="other" <?php if ($gender === 'other') echo 'selected'; ?>>Other</option>
      </select>
      <br>
      <input type="submit" value="Submit">
    </form>
  <?php endif; ?>
</body>
</html>
