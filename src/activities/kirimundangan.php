<?php
session_start();
require '../service/connection.php';

$query = "SELECT nama_event FROM plus";
$result = $conn->query($query);

$getEvent = $conn->query("SELECT * FROM plus");

if ($getEvent->num_rows < 1) {
    return redirect("event", "Tambahkan event terlebih dahulu", "error");
}

while ($row = $getEvent->fetch_array()) {
    $nama_events[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kirim WhatsApp</title>
    <link href="../assets/bootstrap-5.3.3-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #7c2946; /* Warna latar belakang */
            color: white;
            font-family: Arial, sans-serif;
        }

        .form-container {
            margin: 20px auto;
            /* Jarak ke atas dan bawah */
            margin-top: 150px;
            background-color: #D2B48C;
            /* Warna latar belakang */
            padding: 20px;
            /* Jarak isi form ke tepi */
            border-radius: 10px;
            /* Sudut melengkung */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Bayangan */
            max-width: 500px;
            /* Batas lebar form */
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .back-button {
            font-size: 1.2rem;
            text-decoration: none;
            color: black;
            padding: 5px;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #cfb997;
        }

        button.btn-submit {
            background-color: white;
            color: black; /* Warna teks */
            padding: 5px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }

        button.btn-submit:hover {
            background-color: #7c2946;
            color: white;
        }
    </style>
</head>

<body>
    <div class="w-100 position-relative">
        <a href="../admin.php" class="back-button">
            <i class="bi bi-arrow-left fs-3"></i>
        </a>
    </div>
    <div class="container">
        <div class="form-container">
            <h2>Form Kirim Undangan</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Tamu</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama tamu" required>
                </div>
                <div class="mb-3">
                    <label for="telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" placeholder="Masukkan nomor telepon" required>
                </div>
                <div class="mb-3">
                <label for="event" class="form-label">Pilih Acara</label>
                <select class="form-control" id="event" name="event" required>
                    <option value="">-- Pilih Acara --</option>
                    <?php foreach ($nama_events as $event) : ?>
                        <option value="<?= $event[0]?>"><?= $event[1]?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-submit w-100 mt-3">Kirim Undangan</button>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <?php
    include '../service/utility.php';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = htmlspecialchars($_POST['nama']);
        $telepon = htmlspecialchars($_POST['telepon']);
        $event_id = htmlspecialchars($_POST['event']);
        $username = $_SESSION['username'];

        // Ambil nama event berdasarkan ID
        $query = $conn->prepare("SELECT nama_event FROM plus WHERE plus_id = ?");
        $query->bind_param("i", $event_id);
        $query->execute();
        $result = $query->get_result();
        $event_data = $result->fetch_assoc();

        $nama_event = $event_data['nama_event'];

        $link = "http://localhost/undangan/undangan/src/activities/undangan.php?undangan=$event_id&nama=$nama";


        // // Variabel untuk pesan otomatis
        // $username = "Acara Hebat"; // Ganti dengan username Anda
        // $nama_event = "Pesta Pernikahan"; // Ganti dengan nama acara Anda
        // $link = "https://example.com/undangan"; // Ganti dengan link acara Anda

        // Membuat pesan otomatis
        $pesan = "Halo $nama, kami dari $username turut mengundang Anda di acara $nama_event, silahkan akses melalui link berikut: $link";

        $token = "D9BdisAz1PfVEYqbgatK";
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $telepon,
                'message' => $pesan,
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            $_SESSION['error'] = $error_msg;
        } else {
            $_SESSION['success'] = 'Undangan terkirim!';
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>

    <?php
    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '" . $_SESSION['success'] . "',
                showConfirmButton: true
            });
        </script>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '" . $_SESSION['error'] . "',
                showConfirmButton: true
            });
        </script>";
        unset($_SESSION['error']);
    }
    ?>

</body>

</html>
