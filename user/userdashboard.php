<?php
session_start();
include '../db/configdb.php';

if (!isset($_SESSION['email'])) {
    header("Location: login");
    exit;
}

$email = $_SESSION['email'];

// Query untuk mendapatkan nama lengkap dari tabel users
$sqlUser = "SELECT namalengkap FROM users WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();

if ($user) {
    $namalengkap = $user['namalengkap'];
} else {
    $namalengkap = "Nama Tidak Ditemukan"; // Atau penanganan lain sesuai kebutuhan
}

// Query untuk mendapatkan data persetujuan berdasarkan email_pengaju
$sqlApproval = "SELECT * FROM persetujuan WHERE email_desa = ?";
$stmtApproval = $conn->prepare($sqlApproval);
$stmtApproval->bind_param("s", $email);
$stmtApproval->execute();
$resultApproval = $stmtApproval->get_result();

// Query untuk mendapatkan jumlah notifikasi dari tabel pengajuanrequest
$sqlNotifications = "SELECT COUNT(*) AS num_notifications FROM pengajuanrequest WHERE email_tujuan = ?";
$stmtNotifications = $conn->prepare($sqlNotifications);
$stmtNotifications->bind_param("s", $email);
$stmtNotifications->execute();
$resultNotifications = $stmtNotifications->get_result();
$rowNotifications = $resultNotifications->fetch_assoc();

$num_notifications = $rowNotifications ? $rowNotifications['num_notifications'] : 0;

// Query untuk mendapatkan data pendataan berdasarkan email
$sql = "SELECT * FROM pendataan WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div id='content' class='bg-gray-100'>";
    echo "<h2>Data Pendataan di Desa Anda!</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>ID</th><th>Lurah Desa</th><th>Jenis Pangan</th><th>Distributor</th><th>GPS</th><th>Email Desa Anda</th><th>Total Harga</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['lurah_desa']) . "</td>";
        echo "<td>";
        $jenis_pangan = explode(',', $row['jenis_pangan']);
        $berat_pangan = explode(',', $row['berat_pangan']);
        $harga_satuan = explode(',', $row['harga_satuan']);
        foreach ($jenis_pangan as $index => $jenis) {
            echo htmlspecialchars($jenis) . " " . htmlspecialchars($berat_pangan[$index]) . " TON - Rp " . number_format($harga_satuan[$index], 0, '', ',') . "<br>";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($row['distributor']) . "</td>";
        echo "<td><a href='https://www.google.com/maps/search/?api=1&query=" . urlencode($row['gps']) . "' target='_blank'>Buka di Google Maps</a></td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . number_format((float)$row['total_harga'], 0, '', ',') . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    echo "<div id='content' class='bg-gray-100'><h2>Belum ada data di desa anda!</h2></div>";
}

if ($resultApproval->num_rows > 0) {
    echo "<div id='content' class='bg-gray-100'>";
    echo "<h2>Data Persetujuan Pengiriman</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>ID</th><th>Lurah Desa</th><th>Jenis Pangan</th><th>Distributor</th><th>GPS</th><th>Email Pengaju</th><th>Total Harga</th><th>Actions</th></tr></thead>";
    echo "<tbody>";
    while ($rowApproval = $resultApproval->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $rowApproval['id'] . "</td>";
        echo "<td>" . htmlspecialchars($rowApproval['balai_desa']) . "</td>";
        echo "<td>";
        $jenis_pangan = explode(',', $rowApproval['jenis_pangan']);
        $berat_pangan = explode(',', $rowApproval['berat_pangan']);
        $harga_satuan = explode(',', $rowApproval['harga_satuan']);
        foreach ($jenis_pangan as $index => $jenis) {
            echo htmlspecialchars($jenis) . " " . htmlspecialchars($berat_pangan[$index]) . " TON - Rp " . number_format($harga_satuan[$index], 0, '', ',') . "<br>";
        }
        echo "</td>";
        echo "<td>" . htmlspecialchars($rowApproval['distributor']) . "</td>";
        echo "<td><a href='https://www.google.com/maps/search/?api=1&query=" . urlencode(htmlspecialchars($rowApproval['balai_desa'])) . "' target='_blank'>Buka di Google Maps</a></td>";
        echo "<td>" . htmlspecialchars($rowApproval['email_pengaju']) . "</td>";
        echo "<td>" . number_format($rowApproval['total_harga'], 2) . "</td>";
        echo "<td><a href='print_action.php?id=" . $rowApproval['id'] . "'><i class='fas fa-print'></i></a> <a class='approve-action' href='#' data-id='" . $rowApproval['id'] . "' style='color: green;'><i class='fas fa-check'></i></a></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<div id='content'><h2>Belum ada data persetujuan.</h2></div>"; // Menampilkan pesan jika tidak ada data persetujuan
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ddap/src/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/introjs.min.css" />
    <style>
        #notificationTooltip {
            position: relative;
        }
        #content {
            margin-left: 220px;
            padding: 20px;
            padding-top: 140px;
        }
        .navbar-text {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
        }
        .modal-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .notification-group {
            display: flex;
            align-items: center;
            position: relative;
        }
        .bell-icon {
            font-size: 1.5rem;
        }
        .notification-bubble {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 0.45rem;
        }
        .sidebar ul {
            flex-grow: 1;
        }
        .sidebar .help-item {
            margin-top: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
<nav class="bg-white left-1 p-4 fixed w-full z-10 top-0 ml-[220px]">
    <div class="flex justify-between items-center">
        <a href="#" class="text-black font-semibold text-2xl">Dashboard</a>
        <div class="flex items-center">
            <div class="mr-6">Selamat datang, <?php echo $namalengkap; ?></div>
            <div id="notificationTooltip" class="mr-6 relative">
                <i class="ri-account-circle-line text-3xl"></i>
            </div>
            <a href="logout" class="mr-[250px]">Keluar <i class="ri-logout-box-line ml-1"></i></a>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="fixed bg-gray-900 left-0 top-0 w-56 h-full z-50 pr-4 flex flex-col sidebar">
    <a class="flex items-center pb-4 border-b border-b-gray-800 mb-10 rounded" href="#">
        <img src="../assets/img/logo/logo%20thriveterra%20putih.svg" alt="Logo Thrive Terra" class="w-full">
    </a>
    <ul class="mt-4 flex flex-col flex-grow">
        <li class="mb-1 group active" data-step="1" data-title="Dashboard" >
            <a href="userdashboard.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-dashboard-horizontal-line mr-3 text-lg"></i>
                <span class="text-sm">Dashboard</span>
            </a>
        </li>
        <li class="mb-1 group active" data-step="2" data-title="Pendataan"  data-intro="Ini adalah tempat anda melakukan pendataan desa anda.">
            <a href="pendataan.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-database-2-line mr-3 text-lg"></i>
                <span class="text-sm">Pendataan</span>
            </a>
        </li>
        <li class="mb-1 group active" data-step="3" data-title="Pengajuan"  data-intro="Disini tempat anda melakukan pengajuan terhadap desa lain.">
            <a href="permintaan.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-git-pull-request-line mr-3 text-lg"></i>
                <span class="text-sm">Pengajuan</span>
            </a>
        </li>
        <li class="mb-1 group active" data-step="4" data-title="Persetujuan/Riwayat Persetujuan"  data-intro="Di sini tempat anda untuk melakukan persetujuan dan tempat riwayat persetujuan.">
            <a href="notifikasi.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-checkbox-multiple-line mr-3 text-lg"></i>
                <span class="text-sm">Persetujuan dan Riwayat Persetujuan</span>
            </a>
        </li>
        <li class="mb-1 group active" data-step="5" data-title="Hasil Pengajuan"  data-intro="Tempat anda melihat hasil pengajuan.">
            <a href="hasilpengajuan.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-booklet-line mr-3 text-lg"></i>
                <span class="text-sm">Hasil Pengajuan</span>
            </a>
        </li>
    </ul>
    <li class="mb-1 group active help-item">
        <a href="#" id="help-button" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
            <i class="ri-question-line mr-3 text-lg"></i>
            <span class="text-sm">Bantuan</span>
        </a>
    </li>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/intro.min.js"></script>
<script>
    $(document).ready(function() {
        $('#help-button').click(function(event) {
            event.preventDefault(); // Prevent the default action of the link
            introJs().setOptions({
                steps: [
                    {
                        intro: "Welcome to the dashboard! Let's take a quick tour.",
                    },
                    {
                        element: document.querySelector('[data-step="1"]'),
                        intro: "Ini adalah dashboard tempat anda melihat data anda.",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('[data-step="2"]'),
                        intro: "Ini adalah tempat melakukan pendataan pangan desa Anda.",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('[data-step="3"]'),
                        intro: "Ini adalah tempat Anda melakukan pengajuan pangan terhadap desa yang mengalami surplus pangan.",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('[data-step="4"]'),
                        intro: "Disini tempat anda melakukan persetujuan pengajuan pangan dan tempat melihat riwayat persetujuan yang telah anda lakukan.",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('[data-step="5"]'),
                        intro: "Disini tempat anda melihat hasil pengajuan anda.",
                        position: 'right'
                    },
                ],
                showBullets: false,
                showProgress: true,
                exitOnOverlayClick: false,
                nextLabel: 'Next',
                prevLabel: 'Back',
                skipLabel: 'Skip',
                doneLabel: 'Finish',
                disableInteraction: true
            }).start();
        });

        $(document).on('click', '.approve-action', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan memindahkan data ke riwayat persetujuan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, pindahkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'checklist_action.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Muat ulang halaman setelah berhasil
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>
