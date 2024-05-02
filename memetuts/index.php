<?php
require_once "library.php";
require_once "Buku.php";
session_start();

if (!isset($_SESSION['library'])) {
    $_SESSION['library'] = new library();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tambahBuku'])) {
        $isbn = $_POST['isbn'];
        $judul = $_POST['judul'];
        $penulis = $_POST['penulis'];
        $penerbit = $_POST['penerbit'];
        $tahun = $_POST['tahun'];

        $newBook = new ReferenceBook($judul, $penulis, $tahun, $isbn, $penerbit);
        $_SESSION['library']->tambahBuku($newBook);
    }
    if (isset($_POST['hapusBuku'])) {

        if (isset($_POST['isbn'])) {

            $isbn = $_POST['isbn'];
            if (isset($_SESSION['library'])) {
                $_SESSION['library']->hapusBuku($isbn);
            }
        }
    }

    if (isset($_POST['pinjamBuku'])) {
        $isbn = $_POST['isbn'];
        $peminjam = $_POST['peminjam'];
        $tanggal_kembali = $_POST['tanggal'];

        if ($_SESSION['library']->cekLimitPinjam($peminjam)) {
            $book = $_SESSION['library']->cariBukuByISBN($isbn);

            if ($book) {
                $book->pinjamBuku($peminjam, $tanggal_kembali);
                $_SESSION['library']->saveKeSession();
            }
        }
    }

  
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>library memet</title>
    
    <style>
        .card-book {
            width: 200px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="bukubalik.php">
                kembalikan buku
            </a>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="ml-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari buku" name="keyword" aria-label="Cari buku" aria-describedby="button-addon2">
                    <button class="btn btn-outline-light" type="submit" id="button-addon2">Cari</button>
                </div>
            </form>
        </div>
    </nav>
    <div class="modal fade" id="modalPinjam" tabindex="-1" aria-labelledby="modalLabelPinjam" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabelPinjam">Pinjam Buku?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="pinjamISBN" name="isbn" required>
                        <div class="mb-3">
                            <label for="modalPeminjam" class="form-label">Nama Peminjam</label>
                            <input type="text" class="form-control" name="peminjam" id="modalPeminjam" required>
                        </div>
                        <div class="input-group date mb-3" id="datepicker">
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="pinjamBuku" class="btn btn-success">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalLabelHapus" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabelHapus">Hapus Buku?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus buku ini?</p>
                        <input type="hidden" name="isbn" id="hapusISBN" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="hapusBuku" class="btn btn-success">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="mx-auto px-5 my-3 d-flex justify-content-end align-items-end">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="sort" class="form-label">Sortir Berdasarkan</label>
                    <div class="d-flex gap-2"><select class="form-select" aria-label="Sortir Buku" id="sort" name="sort">
                            <option selected value="penulis">Penulis</option>
                            <option value="tahun">Tahun Terbit</option>
                        </select>
                        <button type="submit" name="apply_sort" class="btn btn-success"><i class="bi bi-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="book-container text-center">
            <h3>Daftar Buku yang dapat dipinjam</h3>
            <div class="list-group">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_sort'])) {
                    $sortCriteria = $_POST['sort'];

                    $sortedBooks = $_SESSION['library']->urutkanBuku($sortCriteria);

                    foreach ($sortedBooks as $book) {
                        if (!$book->dipinjam()) {
                            echo "<div class='list-group-item'>";
                            echo "<div class='d-flex'>";
                            echo "<div class='left text-start flex-grow-1'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->getJudul() . "</h5>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenulis() . " - " . $book->getTahunTerbit() . "</h6>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class='right d-flex gap-2 align-items-center'>";
                            echo "<a type='button' class='btn btn-success btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i></a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i></a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
                    $keyword = $_POST['keyword'];
                    $searchResults = $_SESSION['library']->cariBuku($keyword);
                    if (sizeof($searchResults) > 0) {
                        foreach ($searchResults as $book) {
                            if (!$book->dipinjam()) {
                                echo "<div class='list-group-item'>";
                                echo "<div class='d-flex'>";
                                echo "<div class='left text-start flex-grow-1'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . $book->getJudul() . "</h5>";
                                echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenulis() . " - " . $book->getTahunTerbit() . "</h6>";
                                echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='right d-flex gap-2 align-items-center'>";
                                echo "<a type='button' class='btn btn-success btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i></a>";
                                echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i></a>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                    } else {
                        echo "<p>Tidak ada buku dengan judul dan penulis $keyword</p>";
                    }
                } else {
                    foreach ($_SESSION['library']->getSemuaBuku() as $book) {
                        if (!$book->dipinjam()) {
                            echo "<div class='list-group-item'>";
                            echo "<div class='d-flex'>";
                            echo "<div class='left text-start flex-grow-1'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->getJudul() . "</h5>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenulis() . " - " . $book->getTahunTerbit() . "</h6>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class='right d-flex gap-2 align-items-center'>";
                            echo "<a type='button' class='btn btn-success btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i></a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i></a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                }
                ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card my-3">
                    <div class="card-header">
                        Tambahkan Buku
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label for="inISBN" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="inISBN" name="isbn" required>
                            </div>
                            <div class="mb-3">
                                <label for="inJudul" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="inJudul" name="judul" required>
                            </div>
                            <div class="mb-3">
                                <label for="inPenulis" class="form-label">Penulis</label>
                                <input type="text" class="form-control" id="inPenulis" name="penulis" required>
                            </div>
                            <div class="mb-3">
                                <label for="inPenerbit" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="inPenerbit" name="penerbit" required>
                            </div>
                            <div class="mb-3">
                                <label for="inTahun" class="form-label">Tahun Terbit</label>
                                <input type="number" min="1900" max="2099" step="1" class="form-control" id="inTahun" name="tahun" required>
                            </div>
                            <button type="submit" name="tambahBuku" class="btn btn-success"><i class="bi bi-plus"></i></button>
                        </form>
                    </div>
                </div>
            </div>
           
    
    <script>
        $(document).on("click", ".btn-hapus", function() {
            var isbn = $(this).data('isbn');
            $(".modal-body #hapusISBN").val(isbn);
        });
        $(document).on("click", ".btn-pinjam", function() {
            var isbn = $(this).data('isbn');
            $(".modal-body #pinjamISBN").val(isbn);
        });
    </script>
</body>

</html>