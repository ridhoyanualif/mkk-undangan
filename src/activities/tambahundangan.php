<?php
session_start();
require '../service/connection.php'; // Pastikan koneksi database

// Proses penyimpanan data ke database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_undangan = $_POST['judul_undangan'];
    $nama_event = $_POST['nama_event'];
    $tanggal_event = $_POST['tanggal_event'];
    $alamat_event = $_POST['alamat_event'];
    $template = $_POST['template'];
    $id = $_SESSION['id']; 
    
    $image_event = upload();

    if (!$image_event) {
        return false;
    }

    $sql = "INSERT INTO plus (judul_undangan, nama_event, image_event, tanggal_event, alamat_event, template, id)
            VALUES ('$judul_undangan','$nama_event', '$image_event', '$tanggal_event', '$alamat_event', $template, $id)";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Undangan berhasil disimpan.";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

function upload()
{
    $namaFile = $_FILES['image_event']['name'];
    $ukuranFile = $_FILES['image_event']['size'];
    $error = $_FILES['image_event']['error'];
    $tmpName = $_FILES['image_event']['tmp_name'];

    if ($error === 4) {
        echo "<script>
                alert('Pilih gambar terlebih dahulu!');
                window.location.href='tambahundangan.php';
              </script>";
        return false;
    }

    if ($ukuranFile > 1000000) {
        echo "<script>
                alert('Ukuran gambar terlalu besar!');
                window.location.href='tambahundangan.php';
              </script>";
        return false;
    }

    // Generate a unique file name to avoid conflicts
    $fileExt = pathinfo($namaFile, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $fileExt;

    $uploadDir = realpath(__DIR__ . '/../img/image-event') . '/';
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($tmpName, $uploadPath)) {
        return $newFileName;
    } else {
        echo "<script>
                alert('Gagal mengupload file!');
                window.location.href='tambahundangan.php';
              </script>";
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Undangan</title>
    <!-- Bootstrap CSS -->
    <link href="../assets/bootstrap-5.3.3-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        @keyframes move-cloud-left {
            0% {
                transform: translateX(100%);
                /* Mulai di luar layar sebelah kanan */
            }

            100% {
                transform: translateX(-100%);
                /* Berakhir di luar layar sebelah kiri */
            }
        }

        @keyframes move-cloud-right {
            0% {
                transform: translateX(-100%);
                /* Mulai di luar layar sebelah kiri */
            }

            100% {
                transform: translateX(100%);
                /* Berakhir di luar layar sebelah kanan */
            }
        }

        .cloud-left {
            position: absolute;
            top: 0%;
            left: 0;
            width: 120%;
            /* 50% dari lebar layar */
            max-width: 2000px;
            /* Maksimal lebar */
            height: auto;
            opacity: 0.8;
            z-index: -1;
            animation: move-cloud-right 35s linear infinite;
            /* Durasi 20 detik, berulang */
        }

        .cloud-right {
            position: absolute;
            top: 60%;
            right: 0;
            width: 120%;
            /* 50% dari lebar layar */
            max-width: 2000px;
            /* Maksimal lebar */
            height: auto;
            opacity: 0.8;
            z-index: -1;
            animation: move-cloud-left 35s linear infinite;
            /* Durasi 25 detik, berulang */
        }

        body {
            background-color: #7c2946; /* Warna latar belakang */
            font-family: Arial, sans-serif;
            height: 100%;
            overflow-x: hidden; /* Sembunyikan area di luar layar horizontal */
        }

        .form-container {
            margin: 20px auto;
            /* Jarak ke atas dan bawah */
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

        .template-box {
            height: 200px;
            /* Perkecil tinggi template */
            border: 1px solid #ccc;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .template-box:hover {
            transform: scale(1.05);
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
<img src="../assets/img/cloud1.png" alt="Cloud Left" class="cloud-left">
<img src="../assets/img/cloud2.png" alt="Cloud Right" class="cloud-right">

    <div class="w-100 position-relative">
        <a href="../admin.php" class="back-button">
            <i class="bi bi-arrow-left fs-3"></i>
        </a>
    </div>
    <div class="container">
        <div class="form-container">
            <h4 class="text-center mb-3">Buat Undangan Digital</h4>
            <!-- Pesan berhasil atau error -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Form input -->
            <form action="tambahundangan.php" method="POST" enctype="multipart/form-data">
                <div class="mb-2">
                    <label for="judul_undangan" class="form-label">Judul Undangan</label>
                    <input type="text" class="form-control form-control-sm" id="judul_undangan" name="judul_undangan" placeholder="Masukkan judul undangan" required>
                </div>
                <div class="mb-2">
                    <label for="nama_event" class="form-label">Nama Event</label>
                    <input type="text" class="form-control form-control-sm" id="nama_event" name="nama_event" placeholder="Masukkan nama event" required>
                </div>
                <div class="mb-2">
                    <label for="image_event" class="form-label">Image Event</label>
                    <input type="file" class="form-control form-control-sm" id="image_event" name="image_event" placeholder="Masukkan image event" required>
                </div>
                <div class="mb-2">
                    <label for="tanggal_event" class="form-label">Tanggal Event</label>
                    <input type="date" class="form-control form-control-sm" id="tanggal_event" name="tanggal_event" required>
                </div>
                <div class="mb-2">
                    <label for="alamat_event" class="form-label">Alamat Event</label>
                    <textarea class="form-control form-control-sm" id="alamat_event" name="alamat_event" rows="2" placeholder="Masukkan alamat event" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Template</label>
                    <div class="d-flex justify-content-center gap-5">
                        <!-- Template 1 -->
                        <div class="template-box" onclick="setTemplate(0)" id="template0">
                            <img src="../assets/templates/template1.png" width="150px;" alt="Template 1">
                        </div>
                        <!-- Template 2 -->
                        <div class="template-box" onclick="setTemplate(1)" id="template1">
                            <img src="../assets/templates/template2.png" width="150px;" alt="Template 2">
                        </div>
                    </div>
                    <input type="hidden" name="template" id="selected_template" value="0">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-submit btn-sm mt-3">Simpan Undangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        function setTemplate(value) {
            document.getElementById('selected_template').value = value;
            document.getElementById('template0').style.border = value == 0 ? '2px solid blue' : '1px solid #ccc';
            document.getElementById('template1').style.border = value == 1 ? '2px solid blue' : '1px solid #ccc';
        }
    </script>
</body>

</html>