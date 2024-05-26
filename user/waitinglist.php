<?php
include '../db/configdb.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $license_key = $_POST['license_key'];

    $sql = "SELECT * FROM qrcodes WHERE license_key = '$license_key'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $qrcode = $result->fetch_assoc();
        $email = $qrcode['email'];

        $sql = "SELECT * FROM userrequests WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $sql = "INSERT INTO users (namalengkap, no_hp, provinsi, kabupaten, kecamatan, kelurahan, alamat, email, status, password)
                    VALUES ('{$user['namalengkap']}', '{$user['no_hp']}', '{$user['provinsi']}', '{$user['kabupaten']}', '{$user['kecamatan']}', '{$user['kelurahan']}', '{$user['alamat']}', '{$user['email']}', 'aktif', '{$user['password']}')";

            if ($conn->query($sql) === TRUE) {
                // Delete the user data from the userrequests table
                $sql = "DELETE FROM userrequests WHERE email = '$email'";
                if ($conn->query($sql) === TRUE) {
                    header('Location: login.php');
                    exit;
                } else {
                    echo "Error deleting record: " . $conn->error;
                }
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        } else {
            echo "No user found with this email";
        }
    } else {
        echo "Invalid license key";
    }
}

$conn->close();
?>

<html lang="en">

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/ddapkelompok4/assets/css/user/index.css">
</head>
<body class="bg-gray-100">
<div class="container mx-auto mt-12">
    <h1 class="text-center text-3xl font-bold mb-6">Waiting List</h1>
    <div class="flex justify-center mb-6">
        <video id="preview" class="w-full max-w-xs h-auto border-4 border-black rounded-lg shadow"></video>
    </div>
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label for="license_key" class="block text-gray-700 text-sm font-bold mb-2">License Key:</label>
                    <input type="text" id="license_key" name="license_key" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" class="bg-black hover:bg-[#CAF0F8] hover:text-black transition-colors text-white  py-3 px-6 rounded-full w-full transition-colors duration-200 outline outline-1">
                    Submit
                </button>
            </form>
        </div>
    </div>
</div>

    <script>
        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });
        scanner.addListener('scan', function(content) {
            document.getElementById('license_key').value = content;
        });
        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]).catch(function(e) {
                    console.error('Error starting camera', e);
                });
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error('Error getting cameras', e);
        });
    </script>


</body>

</html>