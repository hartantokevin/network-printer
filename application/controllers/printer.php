<?php
defined('BASEPATH') or exit('No direct script access allowed');
class printer extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */


    public function index()
    {

        // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector("JEFRI LOGISTIK on 190.254.6.6");
        $connector = new Mike42\Escpos\PrintConnectors\NetworkPrintConnector("190.254.6.20", 9100);
        // membuat connector printer ke shared printer bernama "printer_a" (yang telah disetting sebelumnya)
        // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector("smb://190.254.6.6/JEFRI LOGISTIK on 190.254.6.6");
        // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector("smb://190.254.6.20/EPSON L800 Series");
        // membuat objek $printer agar dapat di lakukan fungsinya
        $printer = new Mike42\Escpos\Printer($connector);
        // $printer1 = new Mike42\Escpos\Printer($connector1);

        // membuat fungsi untuk membuat 1 baris tabel, agar dapat dipanggil berkali-kali dgn mudah
        function buatBaris4Kolom($kolom1, $kolom2, $kolom3, $kolom4)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 12;
            $lebar_kolom_2 = 8;
            $lebar_kolom_3 = 8;
            $lebar_kolom_4 = 9;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);
            $kolom4 = wordwrap($kolom4, $lebar_kolom_4, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);
            $kolom4Array = explode("\n", $kolom4);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array), count($kolom4Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");

                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                $hasilKolom4 = str_pad((isset($kolom4Array[$i]) ? $kolom4Array[$i] : ""), $lebar_kolom_4, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3 . " " . $hasilKolom4;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            // return implode($hasilBaris, "\n") . "\n";
            return implode("\n", $hasilBaris) . "\n";;
        }

        // Membuat judul
        $printer->initialize();
        $printer->selectPrintMode(Mike42\Escpos\Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
        $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
        $printer->text("Nama Toko\n");
        $printer->text("\n");

        // Data transaksi
        $printer->initialize();
        $printer->text("Kasir : Badar Wildanie\n");
        $printer->text("Waktu : 13-10-2019 19:23:22\n");

        // Membuat tabel
        $printer->initialize(); // Reset bentuk/jenis teks
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom("Barang", "qty", "Harga", "Subtotal"));
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom("Makaroni 250gr", "2pcs", "15.000", "30.000"));
        $printer->text(buatBaris4Kolom("Telur", "2pcs", "5.000", "10.000"));
        $printer->text(buatBaris4Kolom("Tepung terigu", "1pcs", "8.200", "16.400"));
        $printer->text("----------------------------------------\n");
        $printer->text(buatBaris4Kolom('', '', "Total", "56.400"));
        $printer->text("\n");

        // Pesan penutup
        $printer->initialize();
        $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih telah berbelanja\n");
        $printer->text("http://badar-blog.blogspot.com\n");

        $printer->feed(5); // mencetak 5 baris kosong agar terangkat (pemotong kertas saya memiliki jarak 5 baris dari toner)
        $printer->close();

        // ========================

        // $printer1->initialize();
        // $printer1->selectPrintMode(Mike42\Escpos\Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
        // $printer1->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
        // $printer1->text("Nama Toko\n");
        // $printer1->text("\n");

        // // Data transaksi
        // $printer1->initialize();
        // $printer1->text("Kasir : Badar Wildanie\n");
        // $printer1->text("Waktu : 13-10-2019 19:23:22\n");

        // // Membuat tabel
        // $printer1->initialize(); // Reset bentuk/jenis teks
        // $printer1->text("----------------------------------------\n");
        // $printer1->text(buatBaris4Kolom("Barang", "qty", "Harga", "Subtotal"));
        // $printer1->text("----------------------------------------\n");
        // $printer1->text(buatBaris4Kolom("Makaroni 250gr", "2pcs", "15.000", "30.000"));
        // $printer1->text(buatBaris4Kolom("Telur", "2pcs", "5.000", "10.000"));
        // $printer1->text(buatBaris4Kolom("Tepung terigu", "1pcs", "8.200", "16.400"));
        // $printer1->text("----------------------------------------\n");
        // $printer1->text(buatBaris4Kolom('', '', "Total", "56.400"));
        // $printer1->text("\n");

        // // Pesan penutup
        // $printer1->initialize();
        // $printer1->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
        // $printer1->text("Terima kasih telah berbelanja\n");
        // $printer1->text("http://badar-blog.blogspot.com\n");

        // $printer1->feed(5); // mencetak 5 baris kosong agar terangkat (pemotong kertas saya memiliki jarak 5 baris dari toner)
        // $printer1->close();
    }

    public function qrcode_dev()
    {
        // Load an image from PNG URL
        $im = imagecreatefrompng(
            'https://media.geeksforgeeks.org/wp-content/uploads/geeksforgeeks-13.png'
        );

        // Flip the image
        imageflip($im, 2);

        // View the loaded image in browser
        header('Content-type: image/png');
        imagepng($im);
    }
}
