<?php
include '../db/configdb.php';
session_start();
$error = ''; // Initialize the error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $email;
            header('Location: userdashboard');
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $sql = "SELECT * FROM userrequests WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['email'] = $email;
                header('Location: waitinglist');
                exit;
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
    $_SESSION['error'] = $error; // Store the error message in session
}

$conn->close();
?>


<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ddap/src/index.css">
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        .modal-content {
            transition: transform 0.25s ease;
        }
        /* Initial state of the modal */
        .modal {
            opacity: 0;
            pointer-events: none;
        }
        .modal.active {
            opacity: 1;
            pointer-events: all;
        }
        .modal.active .modal-content {
            transform: translateY(0);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen text-sm text-gray-700">
<div class="w-full max-w-lg p-8 space-y-6 bg-white rounded-lg shadow-[#023e8a] shadow-2xl">
    <h2 class="text-xl font-bold text-center text-gray-600">Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
        <div class="form-group">
            <label for="email" class="block text-gray-700">Email:</label>
            <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-md bg-gray-800 focus:bg-white focus:text-black text-white " required>
        </div>
        <div class="form-group">
            <label for="password" class="block text-gray-700">Password:</label>
            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-md bg-gray-800 focus:bg-white focus:text-black text-white " required>
            <p class="text-xs text-gray-500 mt-1">It must be a combination of minimum 8 letters, numbers, and symbols.</p>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="w-4 h-4 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
        </div>
        <input type="submit" value="Login" class="w-full px-4 py-2 text-white bg-black rounded-full hover:bg-[#CAF0F8] hover:text-black transition-colors outline outline-1">
        <p class="text-center text-sm">
            No account yet? <a href="register.php" class="text-blue-600 hover:underline">Sign Up</a>
        </p>
    </form>
</div>

<!-- Modal for showing error messages -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center" id="errorModal">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-content bg-white w-1/3 border-none shadow-lg rounded p-8 m-auto flex-col justify-center align-center">
        <h4 class="text-red-500 text-lg font-bold">Error</h4>
        <p class="py-4 text-sm text-gray-600" id="errorMessage"></p>
        <div class="flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('errorModal').classList.remove('active');
    }

    window.onload = function() {
        <?php if (!empty($_SESSION['error'])) { ?>
        document.getElementById('errorMessage').textContent = '<?php echo $_SESSION['error']; ?>';
        document.getElementById('errorModal').classList.add('active');
        <?php unset($_SESSION['error']); ?>
        <?php } ?>
    };
</script>
</body>
</html>
