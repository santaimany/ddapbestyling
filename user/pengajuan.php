<?php
include '../db/configdb.php';

if ($conn === null) {
    die("Connection failed: Unable to connect to the database");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lurah_desa = isset($_GET['lurah_desa']) ? urldecode($_GET['lurah_desa']) : null;

// Fetch the email from the pendataan table
$sql = "SELECT email AS email_tujuan, jenis_pangan, berat_pangan, harga_satuan, distributor, lurah_desa, gps FROM pendataan WHERE id = ? AND (lurah_desa = ? OR lurah_desa IS NULL)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $lurah_desa);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();


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

$email_balaidesa_tujuan = isset($data['email_tujuan']) ? $data['email_tujuan'] : '';
if ($data) {
    $jenisPanganArray = explode(", ", $data['jenis_pangan']);
    $beratPanganArray = explode(",", $data['berat_pangan']);
    $hargaSatuanArray = explode(', ', $data['harga_satuan']);
} else {
    echo 'Tidak ada data ditemukan untuk ID ini.';
}

$sql = "SELECT namalengkap, no_hp, alamat, gps, balai_desa FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $nama_lengkap = $user['namalengkap'];
    $no_handphone = $user['no_hp'];
    $alamat = $user['alamat'];
    $gps = $user['gps'];
    $balai_desa = $user['balai_desa'];
} else {
    echo 'Tidak ada data pengguna ditemukan.';
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pengajuan Jenis Pangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/ddap/src/index.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcqpq8QdjwYc2tngkoyvpvdZAmEjSxKM&libraries=places"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="/ddap/src/index.css">

    <script>
        function updateTotalHarga() {
            var totalHarga = 0;
            document.querySelectorAll('.berat-input').forEach(function(input) {
                var jenisId = input.id.split('_')[1];
                var berat = parseFloat(input.value);
                var hargaSatuan = parseFloat(document.getElementById('harga_' + jenisId).value);
                if (!isNaN(berat) && !isNaN(hargaSatuan)) {
                    var subtotal = berat * hargaSatuan;
                    document.getElementById('subtotal_' + jenisId).value = subtotal.toFixed(2);
                    totalHarga += subtotal;
                }
            });
            document.getElementById('total_harga').value = 'Rp ' + totalHarga.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            console.log("Total Harga diupdate: ", document.getElementById('total_harga').value); // Tambahkan log ini
        }

        function toggleBeratInput(checkboxElement) {
            var jenisId = checkboxElement.id.split('_')[1];
            var beratInput = document.getElementById('berat_' + jenisId);
            if (checkboxElement.checked) {
                beratInput.style.display = 'block';
            } else {
                beratInput.style.display = 'none';
                beratInput.value = '';
            }
            updateTotalHarga();
        }

        document.addEventListener("DOMContentLoaded", function() {
            var inputs = document.querySelectorAll('.berat-input');
            inputs.forEach(function(input) {
                input.oninput = function(event) {
                    updateTotalHarga();
                };
            });
        });
    </script>
</head>

<body>


<nav class="bg-white text-black left-1 p-4 fixed w-full z-10 top-0 ml-[220px]">
    <div class="flex justify-between items-center ">
        <a href="#" class="font-semibold text-black text-2xl">Pengajuan</a>
        <div class="flex items-center">
            <div class="mr-6">Selamat datang, <?php echo $namalengkap; ?></div>
            <div id="" class=" mr-6 relative">
                <i class="ri-account-circle-line text-3xl"></i>
            </div>
            <a href="logout" class="mr-[250px]">Keluar <i class="ri-logout-box-line ml-1"></i></a>
        </div>
    </div>
</nav>
<div class="fixed bg-gray-900 left-0 top-0 w-56 h-full z-50 pr-4">
    <a class="flex items-center pb-4 border-b border-b-gray-800 mb-10 rounded" href="#">
        <img src="../assets/img/logo/logo%20thriveterra%20putih.svg" alt="Logo Thrive Terra" class="w-full">
    </a>
    <ul class="mt-4">
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
        <li class="mb-1 group active">
            <a href="pendataan.php" class="flex items-center py-2 px-4 text-gray-300 hover:bg-gray-950 rounded-md">
                <i class="ri-settings-3-line mr-3 text-lg"></i>
                <span class="text-sm">Pengaturan</span>
            </a>
        </li>
    </ul>
</div>
<div class=" ml-[150px] mt-14">
    <div class="container ">
        <h2>Pengajuan Jenis Pangan</h2>
        <form action="submit_pengajuan.php" method="post">
            <div class="form-group">
                <label for="lurah_desa">Balai Desa:</label>
                <input type="text" id="lurah_desa" name="lurah_desa" class="form-control" value="<?php echo htmlspecialchars($data['lurah_desa']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="email_tujuan">Email Tujuan:</label>
                <input type="email" id="email_tujuan" name="email_tujuan" class="form-control" value="<?php echo htmlspecialchars($email_balaidesa_tujuan); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="gps">GPS:</label>
                <input type="text" id="gps" name="gps" class="form-control" value="<?php echo htmlspecialchars($data['gps']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="distributor">Distributor:</label>
                <input type="text" id="distributor" name="distributor" class="form-control" value="<?php echo htmlspecialchars($data['distributor']); ?>" readonly>
            </div>

            <h4>Identitas Balai Desa</h4>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($nama_lengkap); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="no_handphone">No Handphone:</label>
                <input type="text" id="no_handphone" name="no_handphone" class="form-control" value="<?php echo htmlspecialchars($no_handphone); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" class="form-control" value="<?php echo htmlspecialchars($alamat); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['email']; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="balai_desa">Asal Balai Desa:</label>
                <input type="text" id="balai_desa" name="balai_desa" class="form-control" value="<?php echo htmlspecialchars($balai_desa); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Pilih Jenis Pangan:</label>
                <?php foreach ($jenisPanganArray as $index => $jenis) : ?>
                    <?php if (!empty($beratPanganArray[$index]) && $beratPanganArray[$index] != '0') : ?>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="jenis_<?php echo htmlspecialchars($jenis); ?>" name="jenis_pangan[]" value="<?php echo htmlspecialchars($jenis); ?>" onchange="toggleBeratInput(this)">
                                <label class="form-check-label" for="jenis_<?php echo htmlspecialchars($jenis); ?>"><?php echo htmlspecialchars($jenis); ?></label>
                            </div>
                            <input type="number" class="form-control berat-input" id="berat_<?php echo htmlspecialchars($jenis); ?>" name="berat_pangan[<?php echo htmlspecialchars($jenis); ?>]" placeholder="Masukkan berat per ton '<?php echo htmlspecialchars($jenis); ?>' dari 1-<?php echo htmlspecialchars($beratPanganArray[$index] ?? '0'); ?> ton" min="1" max="<?php echo htmlspecialchars($beratPanganArray[$index] ?? '0'); ?>" style="display: none;" oninput="updateTotalHarga()">
                            <input type="hidden" class="form-control" id="harga_<?php echo htmlspecialchars($jenis); ?>" name="harga_satuan[<?php echo htmlspecialchars($jenis); ?>]" value="<?php echo htmlspecialchars($hargaSatuanArray[$index] ?? '0'); ?>" readonly>
                            <input type="text" class="form-control" id="subtotal_<?php echo htmlspecialchars($jenis); ?>" name="subtotal[<?php echo htmlspecialchars($jenis); ?>]" readonly>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <label>Total Harga: </label>
            <input type="text" id="total_harga" name="total_harga" class="form-control" value="Rp 0" readonly>
            <br>
            <button type="submit" class="btn btn-primary">Submit Pengajuan</button>
        </form>
    </div>
</div>

</form>
</div>
</body>

</html>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            var totalHarga = document.getElementById('total_harga').value;
            console.log("Total Harga yang dikirim: ", totalHarga); // Debugging untuk melihat nilai yang dikirim
            if (totalHarga === 'Rp 0') {
                event.preventDefault(); // Mencegah form dari disubmit jika total harga belum di-set
                alert('Total harga belum dihitung atau masih nol.');
            }
        });
    });
</script>