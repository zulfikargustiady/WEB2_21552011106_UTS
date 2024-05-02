<?php

class Buku
{
    private $judul;
    private $penulis;
    private $tahun_terbit;
    private $dipinjam = false;
    private $peminjam;
    private $tanggal_kembali;
    private $denda;

    public function __construct($judul, $penulis, $tahun_terbit)
    {
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun_terbit = $tahun_terbit;
    }

    public function getJudul()
    {
        return $this->judul;
    }

    public function getPenulis()
    {
        return $this->penulis;
    }

    public function getTahunTerbit()
    {
        return $this->tahun_terbit;
    }

    public function dipinjam()
    {
        return $this->dipinjam;
    }

    public function pinjamBuku($borrower, $returnDate)
    {
        if (!$this->dipinjam) {
            $this->dipinjam = true;
            $this->peminjam = $borrower;
            $this->tanggal_kembali = $returnDate;
        }
    }

    public function kembalikanBuku()
    {
        if ($this->dipinjam) {
            $tanggal_kembali = new DateTime($this->tanggal_kembali);
            if ($tanggal_kembali < new DateTime()) {
                $this->denda = 5000;
                echo "<script>alert('Buku berhasil dikembalikan. Anda terkena denda sebanyak {$this->denda}');</script>";
            } else {
                echo "<script>alert('Buku berhasil dikembalikan. Tidak ada denda yang dikenakan');</script>";
            }

            $this->dipinjam = false;
            $this->peminjam = "";
            $this->tanggal_kembali = "";
        }
    }
}

class ReferenceBook extends Buku
{
    private $isbn;
    private $penerbit;

    public function __construct($judul, $penulis, $tahun_terbit, $isbn, $penerbit)
    {
        parent::__construct($judul, $penulis, $tahun_terbit);
        $this->isbn = $isbn;
        $this->penerbit = $penerbit;
    }

    public function getISBN()
    {
        return $this->isbn;
    }

    public function getPenerbit()
    {
        return $this->penerbit;
    }
}
?>
