<?php
session_start();
include '../db/configdb.php';

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "')</script>";
    unset($_SESSION['message']);
}

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Get the email of the currently logged in user
$email = $_SESSION['email'];

// Prepare an SQL statement to get the 'balai_desa' and 'gps' values of the user
$sql = "SELECT balai_desa, gps FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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


// Get the 'balai_desa' and 'gps' values
$balai_desa = $user['balai_desa'];
$gps = $user['gps'];

// Check if the email already exists in the table
$sql = "SELECT * FROM pendataan WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// If the email already exists, set the button text to 'Update'
if ($result->num_rows > 0) {
    $buttonText = 'Update';
} else {
    // If the email doesn't exist, set the button text to 'Kirim'
    $buttonText = 'Kirim';
}

$sql = "SELECT total_harga FROM pendataan WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalHarga = $row['total_harga'];
} else {
    $totalHarga = 0; // Atau nilai default lainnya jika tidak ditemukan
}
$stmt->close();

?>

<!DOCTYPE html>
<html>

<head>

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

<body>
<nav class="bg-white text-black left-1 p-4  fixed w-full z-10 top-0 ml-[220px]">
    <div class="flex justify-between items-center ">
        <a href="#" class="text-black font-semibold text-2xl">Pendataan</a>
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


<div class="flex-1 flex items-center justify-center p-8" style="margin-left: 16rem;">
    <div class="w-full ">
        <div class="tab-content  bg-white p-6 rounded-lg mt-10" id="myTabContent">
            <div class=" gap-4 mb-4">
                <!-- Form Pengajuan -->
                <form id="formPendataan" method="post" action="formpendataan.php">
                    <div class="form-group grid grid-cols-1 gap-4">
                        <label for="lurah_desa" class="block text-sm font-bold">Lurah/Desa: </label>
                        <span style="user-select: none;" >Balai Desa </span>
                        <input type="text" class="form-control no-reset  w-full rounded" id="lurah_desa" name="lurah_desa" value="<?php echo $balai_desa; ?>" readonly> <!-- <input type="hidden" name="lurah_desa" value="<?php echo $balai_desa; ?>"> -->
                    </div>

                    <style>
                        /*#inputTable {*/
                        /*    display: flex;*/
                        /*    flex-wrap: wrap;*/
                        /*    justify-content: start;*/
                        /*    flex-direction: row;*/
                        /*    !* Baris baru *!*/
                        /*}*/

                        /*.form-group {*/
                        /*    display: flex;*/
                        /*    flex-direction: column;*/
                        /*    align-items: start;*/
                        /*}*/

                        /*#berat_ pangan1 {*/
                        /*    padding: 10px;*/
                        /*    margin-top: 8px;*/
                        /*    bottom: 30%;*/
                        /*}*/

                        /*.form-control {*/
                        /*    width: 100%;*/
                        /*}*/

                        /*input::placeholder {*/
                        /*    color: rgba(0, 0, 0, 0.5);*/
                        /*    !* Change opacity here *!*/
                        /*}*/
                    </style>

                    <div id="inputTable">
                        <div class="form-group grid grid-cols-1 md:grid-cols-2">
                            <label for="jenis_pangan1" class="block text-sm font-bold">Jenis Pangan 1:</label>
                            <input type="text" class="form-control  w-full rounded" id="jenis_pangan1" name="jenis_pangan[]" placeholder="" >
                            <label for="berat_pangan1" class="block text-sm font-bold">Berat per (TON):</label>
                            <input type="number" class="form-control  w-full rounded" id="berat_pangan1" name="berat_pangan[]" placeholder="Berat per (TON)">
                            <label for="harga_satuan1" class="block text-sm font-bold">Harga Satuan (Rp):</label>
                            <input type="text" class="form-control  w-full rounded" id="harga_satuan1" name="harga_satuan[]" placeholder="Harga Satuan (Rp)">
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-white">
                        <button type="button" id="undoInput" class="bg-gray-900  rounded-full px-4  font-semibold hover:text-black  hover:bg-[#ade8f4]  outline outline-1   ">
                            <span>-</span>
                        </button>
                        <span class="text-lg text-black" id="formCount">1</span>
                        <button type="button" id="tambahInput" class="bg-gray-900  rounded-full px-4 font-semibold   hover:bg-[#ade8f4]  hover:text-black outline outline-1">
                            <span>+</span>
                        </button>
                        <button type="button" id="resetInput" class="ml-3 p-1 px-4 bg-amber-50 text-black font-semibold hover:bg-[#caf0f8] hover:text-black outline-1 outline rounded-md">
                            Reset
                        </button>
                    </div>
                    <!--                    <div class="form-group">-->
                    <!--                        <label for="berat">Berat Total (TON):</label>-->
                    <!--                        <input type="number" class="form-control" id="berat" name="berat" readonly>-->
                    <!--                    </div>-->
                    <div class="form-group">
                        <label for="total_harga">Total Harga Pangan Saat Ini:</label>
                        <input type="text" class="form-control" id="total_harga_display" name="total_harga_display" value="<?php echo 'Rp. ' . number_format($totalHarga, 2, ',', '.'); ?>" readonly>
                        <input type="hidden" name="total_harga_hidden" id="total_harga_hidden" value="<?php echo $totalHarga; ?>">
                    </div>
                    <div class="form-group">
                        <label for="distributor">Distributor:</label>
                        <input type="text" class="form-control" id="distributor" name="distributor">
                    </div>
                    <div class="form-group">
                        <label for="gps">GPS/Lokasi:</label>
                        <input type="text" class="form-control" id="gps" name="gps" value="<?php echo $gps; ?>" readonly>
                    </div>
                    <div id="mapid1" style="height: 500px;" class="z-0"></div>
                    <br>
                    <br>
                    <br>
                    <div class="text-white">
                        <button type="reset" class=" p-2 px-4 m-2 font-semibold hover:bg-[#caf0f8] hover:text-black outline-1 outline rounded-md bg-gray-900  ">Batal</button>
                        <button type="submit" class="p-2 px-6 bg-amber-50 text-black font-semibold hover:bg-[#caf0f8] hover:text-black outline-1 outline rounded-md" id="submitBtn" style="display: none;"><?php echo $buttonText; ?></button>
                    </div>

                </form>

            </div>
            <div class="tab-pane fade" id="permintaan" role="tabpanel" aria-labelledby="permintaan-tab">
                <?php include 'permintaan.php'; ?>
            </div>
            <!-- Your content for this tab goes here -->
        </div>
    </div>
</div>

</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/intro.min.js"></script>
<script>
    var input = document.getElementById('lurah_desa');
    var autocomplete = new google.maps.places.Autocomplete(input);

    var previousValue = input.value;

    input.addEventListener('focus', function() {
        if (!this.value.startsWith('Balai Desa ')) {
            this.value = 'Balai Desa ' + this.value;
        }
        previousValue = this.value;
    });

    input.addEventListener('click', function() {
        if (window.getSelection().toString().startsWith('Balai Desa ')) {
            this.setSelectionRange('Balai Desa '.length, this.value.length);
        }
    });

    input.addEventListener('input', function() {
        if (!this.value.startsWith('Balai Desa ')) {
            this.value = 'Balai Desa ' + this.value;
        }
    });

    input.addEventListener('keydown', function(e) {
        var selectionStart = this.selectionStart;
        if (selectionStart < 'Balai Desa '.length) {
            e.preventDefault();
        }
    });

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // Cek apakah hasil adalah desa
        if (place.types.includes('sublocality_level_1')) {
            console.log("Balai Desa " + place.name);
        } else {}
    });

    window.onload = function() {
        var input = document.getElementById('lurah_desa');
        var inputValue = input.value;

        if (!inputValue.includes('Balai Desa')) {
            input.value = 'Balai Desa ' + inputValue;
        }
    }
</script>


<script>
    // Initialize the map for 'mapid1'
    var map1 = L.map('mapid1').setView([-34.397, 150.644], 13);

    // Add the tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map1);

    // Initialize a variable to hold the marker
    var marker1;

    // Get the GPS coordinates from the input
    var gpsInput = document.getElementById('gps');
    var gpsCoords = gpsInput.value.split(',');

    // Add a marker at the GPS coordinates
    marker1 = L.marker([parseFloat(gpsCoords[0]), parseFloat(gpsCoords[1])]).addTo(map1);

    // Set the map view to the GPS coordinates
    map1.setView([parseFloat(gpsCoords[0]), parseFloat(gpsCoords[1])]);

    // Add geocoder control to the map without adding a marker
    var geocoder = L.Control.geocoder({
        geocodeMarker: false,
        defaultMarkGeocode: false
    }).addTo(map1);

    geocoder.on('markgeocode', function(e) {
        // Set the map view to the result location without adding a marker
        map1.setView(e.geocode.center);
    });

    // Ensure the map is fully visible
    map1.invalidateSize();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash == '#pendataan2') {
            setTimeout(function() {
                var map2 = L.map('mapid2').setView([-34.397, 150.644], 13);

                // Add the tile layer to the map
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map2);

                // Initialize a variable to hold the marker
                var marker2;

                // Add a click event to the map
                map2.on('click', function(e) {
                    // If a marker already exists, remove it
                    if (marker2) {
                        map2.removeLayer(marker2);
                    }

                    // Add a new marker at the clicked location
                    marker2 = L.marker(e.latlng).addTo(map2);

                    // Update the GPS input with the clicked location
                    var gpsInput = document.getElementById('gps2');
                    gpsInput.value = e.latlng.lat + ',' + e.latlng.lng;

                    // Dispatch an input event to the GPS input
                    var event = new Event('input');
                    gpsInput.dispatchEvent(event);
                });

                // Add geocoder control to the map without adding a marker
                var geocoder2 = L.Control.geocoder({
                    geocodeMarker: false,
                    defaultMarkGeocode: false
                }).addTo(map2);

                geocoder2.on('markgeocode', function(e) {
                    // Set the map view to the result location without adding a marker
                    map2.setView(e.geocode.center);
                });

                // Ensure the map is fully visible
                map2.invalidateSize();
            }, 500); // Delay for 500 milliseconds
        }
    });
</script>

<script>
    // Mencegah user memasukkan simbol tanda baca selain angka 0-9 pada input (harga_satuan1)


    // Menunggu dokumen untuk dimuat sepenuhnya
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan elemen input untuk harga satuan
        var inputHargaSatuan = document.getElementById('harga_satuan1');

        // Menambahkan event listener untuk menangani penekanan tombol pada input harga satuan
        inputHargaSatuan.addEventListener('keypress', function(e) {
            // Mencegah input selain angka
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Menambahkan event listener untuk menangani pemasukan data dari paste pada input harga satuan
        inputHargaSatuan.addEventListener('paste', function(e) {
            // Mencegah aksi paste default
            e.preventDefault();
            // Mengambil data yang dipaste dan memfilter sehingga hanya angka yang diperbolehkan
            var pasteData = e.clipboardData.getData('text');
            var filteredPasteData = pasteData.replace(/[^0-9]/g, '');
            // Memasukkan data yang sudah difilter ke dalam input
            document.execCommand("insertText", false, filteredPasteData);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function checkInputs() {
            var jenisPangan = document.getElementById('jenis_pangan1').value;
            var beratPangan = document.getElementById('berat_pangan1').value;
            var hargaSatuan = document.getElementById('harga_satuan1').value;
            var distributor = document.getElementById('distributor').value;

            // Cek apakah semua inputan telah diisi
            if (jenisPangan && beratPangan && hargaSatuan && distributor) {
                // Jika semua inputan diisi, tampilkan tombol
                document.getElementById('submitBtn').style.display = 'inline-block';
            } else {
                // Jika salah satu inputan kosong, sembunyikan tombol
                document.getElementById('submitBtn').style.display = 'none';
            }
        }

        // Panggil checkInputs setiap kali inputan berubah
        document.getElementById('jenis_pangan1').addEventListener('input', checkInputs);
        document.getElementById('berat_pangan1').addEventListener('input', checkInputs);
        document.getElementById('harga_satuan1').addEventListener('input', checkInputs);
        document.getElementById('distributor').addEventListener('input', checkInputs);

        // Panggil sekali saat halaman dimuat
        checkInputs();
    });
</script>

<script>
    //  menciptakan sistem harus mengisi input jenis_pangan1, berat_pangan1, harga_satuan1
    // secara berurutan

    document.addEventListener('DOMContentLoaded', function() {
        var jenisPangan1 = document.getElementById('jenis_pangan1');
        var beratPangan1 = document.getElementById('berat_pangan1');
        var hargaSatuan1 = document.getElementById('harga_satuan1');

        // Menonaktifkan input untuk berat pangan dan harga satuan pada awalnya
        beratPangan1.disabled = true;
        hargaSatuan1.disabled = true;

        // Fungsi untuk mengaktifkan input berat pangan setelah jenis pangan diisi
        jenisPangan1.addEventListener('input', function() {
            if (jenisPangan1.value.trim() !== '') {
                beratPangan1.disabled = false;
            } else {
                beratPangan1.disabled = true;
                hargaSatuan1.disabled = true;
                alert('Harap isi jenis pangan terlebih dahulu.');
            }
        });

        // Fungsi untuk mengaktifkan input harga satuan setelah berat pangan diisi
        beratPangan1.addEventListener('input', function() {
            if (beratPangan1.value.trim() !== '') {
                hargaSatuan1.disabled = false;
            } else {
                hargaSatuan1.disabled = true;
                alert('Harap isi berat pangan terlebih dahulu.');
            }
        });

        // Fungsi untuk memberikan peringatan jika harga satuan belum diisi saat input berubah
        hargaSatuan1.addEventListener('input', function() {
            if (hargaSatuan1.value.trim() === '') {
                alert('Harap isi harga satuan sebelum melanjutkan.');
            }
        });


        const incrementButton = document.getElementById('tambahInput');
        const decrementButton = document.getElementById('undoInput');
        const resetButton = document.getElementById('resetInput');
        const formCount = document.getElementById('formCount');

        let count = 1; // Mulai dengan 1 form

        incrementButton.addEventListener('click', function() {
            count++; // Menambah jumlah form
            formCount.textContent = count; // Memperbarui tampilan angka
        });

        decrementButton.addEventListener('click', function() {
            if (count > 1) { // Hanya mengurangi jika lebih dari 1
                count--; // Mengurangi jumlah form
                formCount.textContent = count; // Memperbarui tampilan angka
            }
        });

        resetButton.addEventListener('click', function() {
            count = 1; // Reset jumlah form ke 1
            formCount.textContent = count; // Memperbarui tampilan angka
        });
        // Fungsi untuk memeriksa semua input sebelum mengizinkan pengiriman form
        document.getElementById('submitBtn').addEventListener('click', function(event) {
            if (jenisPangan1.value.trim() === '' || beratPangan1.value.trim() === '' || hargaSatuan1.value.trim() === '') {
                event.preventDefault(); // Mencegah pengiriman form
                alert('Harap isi semua input secara berurutan.');
            }
        });
    });
</script>
<script>
    /**
     * Fungsi untuk mengupdate total berat pangan
     * Fungsi ini akan mengambil semua input berat pangan, menghitung totalnya,
     * dan menampilkan hasilnya pada input dengan id 'berat'.
     */
    function updateTotalBerat() {
        // Mengambil semua input berat pangan
        var beratPanganInputs = document.querySelectorAll('input[name="berat_pangan[]"]');
        // Inisialisasi variabel untuk menyimpan total berat
        var totalBerat = 0;
        // Melakukan iterasi pada setiap input berat pangan
        for (var i = 0; i < beratPanganInputs.length; i++) {
            // Menambahkan nilai input ke total berat
            totalBerat += Number(beratPanganInputs[i].value);
        }


        // pastikan totalBerat harus bernilai positif (> 0) sebelum memasukkannya ke input
        // asumsikan logika penghitungan total berat di sini

        if (totalBerat <= 0) {
            document.getElementById('berat').value = '';
        } else {
            document.getElementById('berat').value = totalBerat;
        }
    }

    /**
     * Fungsi untuk memformat angka ke dalam format rupiah
     * @param {angka} angka - Angka yang akan diformat
     * @return {string} Angka yang telah diformat ke dalam bentuk rupiah
     */
    function formatRupiah(angka) {

        // Membalikkan urutan angka dan membaginya menjadi array

        var reverse = angka.toString().split('').reverse().join(''),

            // Mencocokkan setiap tiga angka dan menggabungkannya dengan titik

            ribuan = reverse.match(/\d{1,3}/g);

        // Menggabungkan kembali array menjadi string dan membalikkan urutannya

        ribuan = ribuan.join('.').split('').reverse().join('');

        // Menambahkan prefix(awalan) 'Rp.' dan suffix(akhiran) ',00'

        return 'Rp.' + ribuan + ',00';

    }

    document.addEventListener('DOMContentLoaded', function() {
        var inputBeratPangan = document.getElementById('berat_pangan1');

        inputBeratPangan.addEventListener('input', function() {
            if (inputBeratPangan.value <= 0) {
                inputBeratPangan.value = ''; // Mengosongkan input jika nilai kurang dari atau sama dengan 0
            }
        });
    });


    /**
     * Fungsi untuk mengupdate total harga pangan
     * Fungsi ini akan mengambil semua input harga satuan dan berat pangan, menghitung total harganya,
     * dan menampilkan hasilnya pada input dengan id 'total_harga' dalam format rupiah.
     */
    function updateTotalHarga() {
        // Mengambil semua input harga satuan
        var hargaSatuanInputs = document.querySelectorAll('input[name="harga_satuan[]"]');
        // Mengambil semua input berat pangan
        var beratPanganInputs = document.querySelectorAll('input[name="berat_pangan[]"]');
        // Inisialisasi variabel untuk menyimpan total harga
        var totalHarga = 0;
        // Melakukan iterasi pada setiap input harga satuan dan berat pangan
        for (var i = 0; i < hargaSatuanInputs.length; i++) {
            // Mengkonversi nilai input harga satuan ke dalam angka
            var hargaSatuan = Number(hargaSatuanInputs[i].value);
            var beratPangan = Number(beratPanganInputs[i].value);
            totalHarga += hargaSatuan * beratPangan;
        }
        // Menampilkan total harga pada input dengan id 'total_harga' dalam format rupiah
        document.getElementById('total_harga_display').value = formatRupiah(totalHarga);
        document.getElementById('total_harga_hidden').value = totalHarga;
    }

    document.querySelectorAll('input[name="harga_satuan[]"]').forEach(inputElement => {
        inputElement.addEventListener('input', updateTotalHarga);
    });

    document.getElementById('berat_pangan1').addEventListener('input', updateTotalBerat);

    var penghitung = 2;

    document.getElementById('tambahInput').addEventListener('click', function() {
        // Cek apakah input terakhir sudah diisi semua sebelum menambahkan baris baru
        var lastJenisPangan = document.querySelector('input[name="jenis_pangan[]"]:last-of-type');
        var lastBeratPangan = document.querySelector('input[name="berat_pangan[]"]:last-of-type');
        var lastHargaSatuan = document.querySelector('input[name="harga_satuan[]"]:last-of-type');

        if (!lastJenisPangan || !lastBeratPangan || !lastHargaSatuan || // Jika ini adalah baris pertama
            (lastJenisPangan.value.trim() !== '' && lastBeratPangan.value.trim() !== '' && lastHargaSatuan.value.trim() !== '')) { // Atau jika semua input terakhir sudah diisi
            // Lanjutkan menambahkan input baru
            var inputTable = document.getElementById('inputTable');
            var newFormGroup = document.createElement('div');
            newFormGroup.className = 'form-group grid grid-cols-1 md:grid-cols-2';

            var newLabel = document.createElement('label');
            newLabel.textContent = 'Jenis Pangan ' + penghitung + ':';
            var newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control  ';
            newInput.name = 'jenis_pangan[]';
            newFormGroup.appendChild(newLabel);
            newFormGroup.appendChild(newInput);

            var newLabelBerat = document.createElement('label');
            newLabelBerat.textContent = 'Berat per (TON):';
            var newInputBerat = document.createElement('input');
            newInputBerat.type = 'number';
            newInputBerat.className = 'form-control ';
            newInputBerat.name = 'berat_pangan[]';
            newInputBerat.placeholder = 'Berat per (TON)';
            newInputBerat.disabled = true; // Awalnya nonaktif
            newFormGroup.appendChild(newLabelBerat);
            newFormGroup.appendChild(newInputBerat);

            var newLabelHargaSatuan = document.createElement('label');
            newLabelHargaSatuan.textContent = 'Harga Satuan (Rp):';
            var newInputHargaSatuan = document.createElement('input');
            newInputHargaSatuan.type = 'text';
            newInputHargaSatuan.className = 'form-control';
            newInputHargaSatuan.name = 'harga_satuan[]';
            newInputHargaSatuan.placeholder = 'Harga Satuan (Rp)';
            newInputHargaSatuan.disabled = true; // Awalnya nonaktif
            newFormGroup.appendChild(newLabelHargaSatuan);
            newFormGroup.appendChild(newInputHargaSatuan);

            inputTable.appendChild(newFormGroup);
            penghitung++;

            // Aktifkan input berat pangan setelah jenis pangan diisi
            newInput.addEventListener('input', function() {
                if (newInput.value.trim() !== '') {
                    newInputBerat.disabled = false;
                } else {
                    newInputBerat.disabled = true;
                    newInputHargaSatuan.disabled = true;
                }
            });

            // Aktifkan input harga satuan setelah berat pangan diisi
            newInputBerat.addEventListener('input', function() {
                if (newInputBerat.value.trim() !== '') {
                    newInputHargaSatuan.disabled = false;
                } else {
                    newInputHargaSatuan.disabled = true;
                }
            });

            // Tambahkan event listener untuk memperbarui total harga ketika input harga satuan baru diisi
            newInputHargaSatuan.addEventListener('input', updateTotalHarga);
        } else {
            // Jika input terakhir belum diisi semua, tampilkan alert
            alert('Harap isi semua input pada baris terakhir sebelum menambahkan yang baru.');
        }
    });
    document.getElementById('undoInput').addEventListener('click', function() {
        var inputTable = document.getElementById('inputTable');
        if (inputTable.children.length > 1) {
            inputTable.removeChild(inputTable.lastChild);
            penghitung--;
        }
    });

    document.getElementById('resetInput').addEventListener('click', function() {
        var confirmation = confirm("Apakah anda yakin untuk mereset row yang sudah anda buat?");
        if (confirmation) {
            var inputTable = document.getElementById('inputTable');
            while (inputTable.children.length > 1) {
                inputTable.removeChild(inputTable.lastChild);
            }
            penghitung = 2;

            // Mengambil semua elemen input
            var inputs = document.querySelectorAll('input');

            // Mencegah agar ketika saya menekan tombol resetInput maka kolom input balai_desa tidak
            // mereset hasil dari user balai_desa
            for (var i = 0; i < inputs.length; i++) {
                // Periksa apakah elemen input saat ini tidak memiliki kelas 'no-reset'
                if (!inputs[i].classList.contains('no-reset')) {
                    // Jika elemen input tidak memiliki kelas 'no-reset', atur nilai input menjadi kosong saja ('')
                    inputs[i].value = '';
                }
            }
        }
    });

    var inputs = document.querySelectorAll('input');
    document.getElementById('submitBtn').addEventListener('click', function() {
        var totalHarga = document.getElementById('total_harga').value;
        document.getElementById('total_harga_hidden').value = totalHarga.replace(/[^0-9,-]+/g, "");
    });

    function checkInputs() {
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].value === '') {
                return false;
            }
        }
        return true;
    }

    for (var i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('input', function() {
            if (checkInputs()) {
                submitBtn.style.display = 'inline-block';
            } else {
                submitBtn.style.display = 'none';
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


    // document.getElementById('formPendataan').addEventListener('submit', function (event) {
    //     if (!checkInputs()) {
    //         event.preventDefault();
    //         alert('Harap isi semua kolom input sebelum mengirim.');
    //     }
    // });
</script>