<?php

include '../db/configdb.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];

// Query untuk mendapatkan data dari tabel riwayatpersetujuan berdasarkan email_desa
$query = "SELECT * FROM riwayatpersetujuan WHERE email_desa = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="overflow-x-auto bg-white shadow-md rounded-lg mt-6 p-6 mr-60">
    <h2 class="text-2xl font-bold mb-4">Riwayat Persetujuan</h2>
    <table class="min-w-full bg-white border-collapse block md:table">
        <thead class="block md:table-header-group">
        <tr class="border border-gray-300 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto md:relative">
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">ID</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Lurah Desa</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Distributor</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Nama Lengkap</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">No Handphone</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Alamat</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">GPS</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Email Pengaju</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Balai Desa</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Jenis Pangan</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Berat Pangan</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Harga Satuan</th>
            <th class="bg-gray-200 p-2 text-gray-600 font-bold md:border md:border-gray-300 text-left block md:table-cell">Total Harga</th>
        </tr>
        </thead>
        <tbody class="block md:table-row-group">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr class="bg-gray-100 border border-gray-300 md:border-none block md:table-row">
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo $row['id']; ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['lurah_desa']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['distributor']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['no_handphone']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['gps']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['email_pengaju']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['balai_desa']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['jenis_pangan']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo htmlspecialchars($row['berat_pangan']); ?></td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell">
                    <?php
                    $harga_satuan = explode(',', $row['harga_satuan']);
                    foreach ($harga_satuan as $harga) {
                        echo number_format((float)$harga, 2) . "<br>";
                    }
                    ?>
                </td>
                <td class="p-2 md:border md:border-gray-300 text-left block md:table-cell"><?php echo number_format((float)$row['total_harga'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
