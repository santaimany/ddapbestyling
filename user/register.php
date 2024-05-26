<?php

include '../db/configdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namalengkap = $_POST['namalengkap'];
    $no_hp = $_POST['no_hp'];
    $provinsi = isset($_POST['provinsi']) ? $_POST['provinsi'] : '';
    $kabupaten = isset($_POST['kabupaten']) ? $_POST['kabupaten'] : '';
    $kecamatan = isset($_POST['kecamatan']) ? $_POST['kecamatan'] : '';
    $kelurahan = isset($_POST['kelurahan']) ? $_POST['kelurahan'] : '';
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO userrequests (namalengkap, no_hp, provinsi, kabupaten, kecamatan, kelurahan, alamat, email, password)
        VALUES ('$namalengkap', '$no_hp', '$provinsi', '$kabupaten', '$kecamatan', '$kelurahan', '$alamat', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        header('Location: waitinglist');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/user/index.css">
    <style>
        .gradient-custom {
            background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 25%, rgba(0, 128, 128, 0.25) 50%, rgba(0, 128, 128, 0.5) 75%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center gradient-custom ">
<div class="flex w-full shadow-lg  overflow-hidden">
    <!-- Left Side (Image) -->
    <div class="hidden lg:f lg:block lg:w-1/2 bg-cover bg-gradient-r from-[#00052E] to-[#001F78] " style="background-image: url('../assets/img/register.jpg');">
        <img src="../assets/img/logo/logo%20thriveterra%20putih.svg" class="flex justify-center items-center">
    </div>
    <!-- Right Side (Form) -->
    <div class="w-full lg:w-1/2 bg-white p-8 lg:p-16">
        <div class="flex justify-center mb-8">
            <img src="../assets/img/logo/ThriveTerra_Logo.png" class="w-40 mb-4"/>
        </div>
        <h1 class="text-2xl font-bold mb-6 text-center">Form Pendaftaran - Kepala Desa</h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="namalengkap" class="block text-sm font-semibold mb-2 text-gray-700">Nama Lengkap:</label>
                <input type="text" class="form-control text-black border border-gray-300 rounded w-full p-2" id="namalengkap" name="namalengkap" required>
            </div>
            <div>
                <label for="no_hp" class="block text-sm font-semibold mb-2 text-gray-700">No HP:</label>
                <input type="text" class="form-control border border-gray-300 rounded w-full p-2" id="no_hp" name="no_hp" required>
            </div>
            <div>
                <label for="alamat" class="block text-sm font-semibold mb-2 text-gray-700">Alamat:</label>
                <input type="text" class="form-control border border-gray-300 rounded w-full p-2" id="alamat" name="alamat" required>
            </div>
            <div>
                <label for="select2-provinsi" class="block text-sm font-semibold text-gray-700">Provinsi:</label>
                <select class="form-control border border-gray-300 rounded w-full p-2 mt-2" name="provinsi" id="select2-provinsi" required></select>
            </div>
            <div>
                <label for="select2-kabupaten" class="block text-sm font-semibold mb-2 text-gray-700">Kabupaten:</label>
                <select class="form-control border border-gray-300 rounded w-full p-2" name="kabupaten" id="select2-kabupaten" required></select>
            </div>
            <div>
                <label for="select2-kecamatan" class="block text-sm font-semibold mb-2 text-gray-700">Kecamatan:</label>
                <select class="form-control border border-gray-300 rounded w-full p-2" name="kecamatan" id="select2-kecamatan" required></select>
            </div>
            <div>
                <label for="select2-kelurahan" class="block text-sm font-semibold mb-2 text-gray-700">Kelurahan:</label>
                <select class="form-control border border-gray-300 rounded w-full p-2" name="kelurahan" id="select2-kelurahan" required></select>
            </div>
            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-semibold mb-2 text-gray-700">Email:</label>
                <input type="email" class="form-control border border-gray-300 rounded w-full p-2" id="email" name="email" required>
            </div>
            <div class="md:col-span-2">
                <label for="password" class="block text-sm font-semibold mb-2 text-gray-700">Password:</label>
                <input type="password" class="form-control border border-gray-300 rounded w-full p-2" id="password" name="password" required>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-black hover:bg-[#caf0f8] hover:text-black text-white py-3 px-6 rounded-full w-full duration-200 outline outline-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>




<script src="../assets/js/user/register.ts"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var urlProvinsi = "https://ibnux.github.io/data-indonesia/provinsi.json";
    var urlKabupaten = "https://ibnux.github.io/data-indonesia/kabupaten/";
    var urlKecamatan = "https://ibnux.github.io/data-indonesia/kecamatan/";
    var urlKelurahan = "https://ibnux.github.io/data-indonesia/kelurahan/";



    function clearOptions(id) {
        console.log("on clearOptions :" + id);

        //$('#' + id).val(null);
        $('#' + id).empty().trigger('change');
    }

    console.log('Load Provinsi...');
    $.getJSON(urlProvinsi, function (res) {

        res = $.map(res, function (obj) {
            obj.text = obj.nama
            return obj;
        });

        data = [{
            id: "",
            nama: "- Pilih Provinsi -",
            text: "- Pilih Provinsi -"
        }].concat(res);

        //implemen data ke select provinsi
        $("#select2-provinsi").select2({
            dropdownAutoWidth: true,
            width: '100%',
            data: data
        })
    });

    var selectProv = $('#select2-provinsi');
    $(selectProv).change(function () {
        var value = $(selectProv).val();
        clearOptions('select2-kabupaten');

        if (value) {
            console.log("on change selectProv");

            var text = $('#select2-provinsi :selected').text();
            console.log("value = " + value + " / " + "text = " + text);

            console.log('Load Kabupaten di ' + text + '...')
            $.getJSON(urlKabupaten + value + ".json", function (res) {

                res = $.map(res, function (obj) {
                    obj.text = obj.nama
                    return obj;
                });

                data = [{
                    id: "",
                    nama: "- Pilih Kabupaten -",
                    text: "- Pilih Kabupaten -"
                }].concat(res);

                //implemen data ke select provinsi
                $("#select2-kabupaten").select2({
                    dropdownAutoWidth: true,
                    width: '100%',
                    data: data
                })
            })
        }
    });

    var selectKab = $('#select2-kabupaten');
    $(selectKab).change(function () {
        var value = $(selectKab).val();
        clearOptions('select2-kecamatan');

        if (value) {
            console.log("on change selectKab");

            var text = $('#select2-kabupaten :selected').text();
            console.log("value = " + value + " / " + "text = " + text);

            console.log('Load Kecamatan di ' + text + '...')
            $.getJSON(urlKecamatan + value + ".json", function (res) {

                res = $.map(res, function (obj) {
                    obj.text = obj.nama
                    return obj;
                });

                data = [{
                    id: "",
                    nama: "- Pilih Kecamatan -",
                    text: "- Pilih Kecamatan -"
                }].concat(res);

                //implemen data ke select provinsi
                $("#select2-kecamatan").select2({
                    dropdownAutoWidth: true,
                    width: '100%',
                    data: data
                })
            })
        }
    });

    var selectKec = $('#select2-kecamatan');
    $(selectKec).change(function () {
        var value = $(selectKec).val();
        clearOptions('select2-kelurahan');

        if (value) {
            console.log("on change selectKec");

            var text = $('#select2-kecamatan :selected').text();
            console.log("value = " + value + " / " + "text = " + text);

            console.log('Load Kelurahan di ' + text + '...')
            $.getJSON(urlKelurahan + value + ".json", function (res) {

                res = $.map(res, function (obj) {
                    obj.text = obj.nama
                    return obj;
                });

                data = [{
                    id: "",
                    nama: "- Pilih Kelurahan -",
                    text: "- Pilih Kelurahan -"
                }].concat(res);

                //implemen data ke select provinsi
                $("#select2-kelurahan").select2({
                    dropdownAutoWidth: true,
                    width: '100%',
                    data: data
                })
            })
        }
    });

    var selectKel = $('#select2-kelurahan');
    $(selectKel).change(function () {
        var value = $(selectKel).val();

        if (value) {
            console.log("on change selectKel");

            var text = $('#select2-kelurahan :selected').text();
            console.log("value = " + value + " / " + "text = " + text);
        }
    });

    let lastSubmitTime = 0;
    const submitInterval = 2000; // Interval dalam milidetik (2 detik)

    const handleRegisterClick = (event) => {
        // Mencegah form dikirim secara normal

        const currentTime = Date.now();
        if (currentTime - lastSubmitTime < submitInterval) {
            console.log('Coba lagi nanti - Terlalu cepat.');
            return; // Hentikan eksekusi jika pengiriman terlalu cepat
        }

        lastSubmitTime = currentTime; // Perbarui waktu terakhir pengiriman

        console.log('Form sedang dikirim...');

        // Debug URL
        const redirectUrl = 'http://localhost:63342/ddap-project2%20-%20Copy/ddap/api/user/waitinglist.php?_ijt=dl1l8o3kvo2mo00lag7cgdktag&_ij_reload=RELOAD_ON_SAVE';
        console.log('Redirecting to:', redirectUrl);

        // Setelah pengiriman, alihkan halaman
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 1000); // Tunggu satu detik sebelum redirect untuk simulasi pengiriman data
    }

</script>


