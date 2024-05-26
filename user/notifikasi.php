<?php
session_start();
include '../db/configdb.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];

$query = "SELECT * FROM pengajuanrequest WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$sql = "SELECT * FROM pengajuanrequest WHERE email_tujuan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    die("Error executing the query: " . $stmt->error);
}

$sqlUser = "SELECT namalengkap FROM users WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userName = $resultUser->fetch_assoc();

if ($userName) {
    $namalengkap = $userName['namalengkap'];
} else {
    $namalengkap = "Nama Tidak Ditemukan"; // Atau penanganan lain sesuai kebutuhan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Persetujuan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- FontAwesome CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="/ddap/src/index.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcqpq8QdjwYc2tngkoyvpvdZAmEjSxKM&libraries=places"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/introjs.min.css" />
    <link
            href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"
            rel="stylesheet"
    />
</head>
<body class="bg-gray-100">
<nav class="bg-white text-black left-1 p-6 fixed w-full z-10 top-0 ml-[220px]">
    <div class="flex justify-between items-center ">
        <a href="#" class="text-black font-semibold text-2xl">Persetujuan dan Riwayat Persetujuan</a>
        <div class="flex items-center">
            <div class="mr-6">Selamat datang, <?php echo $namalengkap; ?></div>
            <div id="" class=" mr-6 relative">
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


<div class="container mx-auto mt-16 ml-56 overflow-x-auto w-[1311px]">
    <div class=" p-6  bg-gray-200 shadow-lg">
        <div class="flex justify-between items-center mb-6 mt-10">
            <h1 class="text-2xl  font-bold">Persetujuan</h1>
        </div>
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                <tr>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Desa Anda</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Distributor</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Nama Lengkap</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">No Handphone</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Alamat</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">GPS</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Email</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Tujuan</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Pangan</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Total Harga</th>
                    <th class="py-3 px-4 text-left bg-gray-100 font-bold text-gray-600 uppercase">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class="bg-gray-50 hover:bg-gray-100 transition duration-150 ease-in-out">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['lurah_desa']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['distributor']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['no_handphone']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['alamat']); ?></td>
                        <td class="py-3 px-4">
                            <a href="javascript:void(0);" onclick="window.open('https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['gps']); ?>', '_blank')" class="text-blue-600 hover:underline">Buka di Google Maps</a>
                        </td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['email_pengaju']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['balai_desa']); ?></td>
                        <td class="py-3 px-4">
                            <?php
                            $jenis_pangan_array = explode(", ", $row['jenis_pangan']);
                            $berat_pangan_array = explode(", ", $row['berat_pangan']);
                            $combined_array = array();
                            for ($i = 0; $i < count($jenis_pangan_array); $i++) {
                                $combined_array[] = ($i + 1) . '. ' . htmlspecialchars($jenis_pangan_array[$i]) . ' ' . htmlspecialchars($berat_pangan_array[$i]) . ' ton';
                            }
                            echo implode("<br>", $combined_array);
                            ?>
                        </td>
                        <td class="py-3 px-4">
                            <?php
                            if (is_numeric($row['total_harga'])) {
                                $totalHargaFormatted = number_format($row['total_harga'], 0, '', ',');
                                echo "Rp. " . htmlspecialchars($totalHargaFormatted);
                            } else {
                                echo "Data tidak valid";
                            }
                            ?>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-2">
                                <a href="javascript:void(0);" onclick="promptSetuju(<?php echo $row['id']; ?>);" class="text-green-600 hover:underline" data-tip="Setuju"><i class="fas fa-check"></i></a>
                                <a href="javascript:void(0);" onclick="promptPenolakan(<?php echo $row['id']; ?>);" class="text-red-600 hover:underline" data-tip="Tolak"><i class="fas fa-times"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'riwayatpersetujuan.php'; ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/intro.min.js"></script>
<script>
    function promptPenolakan(id) {
        Swal.fire({
            title: 'Masukkan Alasan Penolakan',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (alasan) => {
                if (!alasan) {
                    Swal.showValidationMessage('Alasan harus diisi.');
                } else {
                    return fetch(`proses_penolakan.php?id=${id}&keterangan=${encodeURIComponent(alasan)}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Penolakan Terkirim!',
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    function promptSetuju(id) {
        Swal.fire({
            title: 'Apakah Anda yakin ingin menyetujui?',
            showCancelButton: true,
            confirmButtonText: 'Setuju',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`proses_persetujuan.php?id=${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Persetujuan Terkirim!',
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

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
