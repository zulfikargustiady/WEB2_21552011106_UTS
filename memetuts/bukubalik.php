<?php
require_once "library.php";
require_once "Buku.php";

session_start();
if (isset($_POST['kembalikanBuku'])) {
    $isbn = $_POST['isbn'];

    $book = $_SESSION['library']->cariBukuByISBN($isbn);

    if ($book) {
        $book->kembalikanBuku();
        $_SESSION['library']->saveKeSession();
    } else {
        echo "<script>alert('Tidak ada buku yang dikembalikan');</script>";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>library memet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <style>
        .card-book {
            width: 200px;
        }
    </style>
</head>

<body>
<div class="col-md-6">
                <div class="card my-3">
                    <div class="card-header">
                        Kembalikan Buku
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <label for="kembaliISBN">Buku</label>
                            <select class="form-select mb-3" aria-label="Default select example" name="isbn" id="kembaliISBN" required>
                                <?php
                                $counter = 0;

                                foreach ($_SESSION['library']->getSemuaBuku() as $book) {
                                    if ($book->dipinjam()) {
                                        echo "<option value='" . $book->getISBN() . "'>" . $book->getJudul() . "</option>";
                                    } else {
                                        $counter++;
                                    }
                                }
                                if ($counter === sizeof($_SESSION['library']->getSemuaBuku())) {
                                    echo "<option value='kosong'>Tidak ada buku yang sedang dipinjam</option>";
                                } ?>
                            </select>
                            <button type="submit" name="kembalikanBuku" class="btn btn-success"><i class="bi bi-save"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

                            </body>
                            </head>
                    </html>