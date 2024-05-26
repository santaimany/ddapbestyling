<?php
session_start();
include '../db/configdb.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email_pengaju = $_SESSION['email'];

// Menangani permintaan AJAX
if (isset($_POST['status'])) {
    $status = $_POST['status'];
    if ($status == 'Diterima') {
        $query = "SELECT * FROM persetujuan WHERE email_pengaju = ?";
    } elseif ($status == 'Ditolak') {
        $query = "SELECT * FROM penolakan WHERE email_pengaju = ?";
    } else {
        $query = "SELECT * FROM pengajuanrequest WHERE email_pengaju = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email_pengaju);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table class='table-auto w-full text-left '>";
    echo "<thead><tr><th>ID</th><th>Nama Lengkap</th><th>Status</th>";
    if ($status == 'Diterima') {
        echo "<th>Detail</th>";
    } elseif ($status == 'Ditolak') {
        echo "<th>Keterangan</th>";
    } else {
        echo "<th>Status</th>";
    }
    echo "</tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td class='border px-4 py-2'>" . $row['id'] . "</td><td class='border px-4 py-2'>" . $row['nama_lengkap'] . "</td><td class='border px-4 py-2'>" . $status . "</td>";
        if ($status == 'Diterima') {
            echo "<td class='border px-4 py-2'><a href='detail.php?id=" . $row['id'] . "' class='text-blue-500 hover:underline'>Lihat Detail</a></td>";
        } elseif ($status == 'Ditolak') {
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['keterangan']) . "</td>";
        } else {
            echo "<td class='border px-4 py-2'>Tunggu</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    exit;
}

$email = $_SESSION['email'];

$sql = "SELECT gps FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_gps = explode(",", $user['gps']);

$sqlUser = "SELECT namalengkap FROM users WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userName = $resultUser->fetch_assoc();

if ($user) {
    $namalengkap = $userName['namalengkap'];
} else {
    $namalengkap = "Nama Tidak Ditemukan"; // Atau penanganan lain sesuai kebutuhan
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pengajuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="/ddap/src/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/introjs.min.css" />

</head>
<body class="bg-gray-100">
<nav class="bg-white text-black left-1 p-6 fixed w-full z-10 top-0 ml-[220px]">
    <div class="flex justify-between items-center ">
        <a href="#" class="text-black text-2xl font-semibold">Hasil Pengajuan</a>
        <div class="flex items-center">
            <div class="mr-6">Selamat datang, <?php echo $namalengkap; ?></div>
            <div id="" class=" mr-6 relative">
                <i class="ri-account-circle-line text-3xl"></i>
            </div>
            <a href="logout" class="mr-[250px]">Keluar <i class="ri-logout-box-line ml-1 "></i></a>
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

<div class="container mx-auto mt-20 p-6 ml-56 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-extralight mb-4 "></h1>
    <select id="statusDropdown" class="form-control mb-4 w-1/2 text-center">
        <option value="Diterima">Diterima</option>
        <option value="Ditolak">Ditolak</option>
        <option value="Pending">Pending</option>
    </select>
    <div id="tableContainer" class="overflow-x-auto"></div>


</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/intro.min.js"></script>
<script>
    $(document).ready(function() {
        $('#statusDropdown').change(function() {
            var status = $(this).val();
            $.post('hasilpengajuan.php', {status: status}, function(data) {
                $('#tableContainer').html(data);
            });
        });

        $('#statusDropdown').trigger('change');
    });

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
