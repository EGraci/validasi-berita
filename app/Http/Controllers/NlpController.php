<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;
use Phpml\FeatureExtraction\TfIdfTransformer;


class NlpController extends Controller
{
    public function index(){
        $artikel1 = "Apabila Anda punya rekening Bank Mandiri, Bank BRI, Bank BNI, Bank BCA, dan ingin ambil uang di ATM, sedangkan di ATM ada stiker Call Mandiri dengan No. Telp 021 33131777, jangan masukkan kartu ATM Anda. Cabut stiker itu, karena stiker itu dapat merekam PIN Anda juga berisi program untuk menguras saldo rekening dalam mesin ATM. Mohon disebarkan ke tema-teman dan family. Itu adalah sindikat baru di Jakarta, Jogja, Surabaya, dan Medan. Sudah banyak korban. Semoga bermanfaat. Ini info valid karena hari ini terjadi kegaduhan di RSCM Jakarta, banyak pegawai dan dokter RSCM rekening Mandiri ditarik dalam jumlah besar padalah mereka tidak bertransaksi. Akhirnya Bank Mandiri mengganti mesin ATM tersebut.";
        $artikel2 = "Jika Anda memiliki rekening Bank Mandiri, Bank BRI, Bank BNI, Bank BCA dan ingin menarik uang dari ATM sedangkan ATM tersebut memiliki stiker Call Mandiri dengan nomor tersebut. Telp. 021 33131777, jangan masukkan kartu bank anda. Lepas stiker karena stiker dapat menyimpan kode PIN Anda dan juga berisi program untuk mengosongkan rekening ATM. Silakan berbagi dengan teman dan keluarga. Ini adalah serikat baru di Jakarta, Yogyakarta, Surabaya dan Medan. Ada banyak korban. Semoga bermanfaat. Ini informasi yang valid karena hari ini terjadi keributan di RSCM Jakarta, banyak staf dan dokter RSCM harus menarik dana dalam jumlah besar dari rekening Mandiri mereka meskipun tidak melakukan transaksi. Belakangan, Bank Mandiri mengganti ATM ";
        $artikel3 = "Nasabah BANK PAPUA anda kami undang ke Cabang BANK PAPUA untuk Tukar Point Transaksi anda besok jam2 info code anda Silahkan klik di sini myads.id/Cekcodebarang";

        $artikel1 = $this->preprocessing($artikel1);
        $artikel2 = $this->preprocessing($artikel2);
        $artikel3 = $this->preprocessing($artikel3);

        $token1 = $this->tokenizer($artikel1);
        $token2 = $this->tokenizer($artikel2);
        $token3 = $this->tokenizer($artikel3);


        similar_text($artikel1, $artikel3, $percent);

        dd($this->jaccard_similarity($token1, $token2));
        return view('Admin/DashboardSeo',[
            'menu' =>null,
        ]);
    }
    public function jaccard_similarity($set1, $set2) {
        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));
        $similarity = $intersection / $union;
        return $similarity;
    }

    public function preprocessing($artikel){
        $stopwords = array(
            "ada", "adalah", "adanya", "adapun", "agak", "agaknya", "agar", "akan", "akankah", "akhir", "akhiri", "akhirnya", "aku", "akulah", "amat", "amatlah", "anda", "andalah", "antar", "antara", "antaranya", "apa", "apaan", "apabila", "apakah", "apalagi", "apatah", "artinya", "asal", "asalkan", "atas", "atau", "ataukah", "ataupun", "awal", "awalnya", "bagai", "bagaikan", "bagaimana", "bagaimanakah", "bagaimanapun", "bagi", "bagian", "bahkan", "bahwa", "bahwasanya", "baik", "bakal", "bakalan", "balik", "banyak", "bapak", "baru", "bawah", "beberapa", "begini", "beginian", "beginikah", "beginilah", "begitu", "begitukah", "begitulah", "begitupun", "bekerja", "belakang", "belakangan", "belum", "belumlah", "benar", "benarkah", "benarlah", "berada", "berakhir", "berakhirlah", "berakhirnya", "berapa", "berapakah", "berapalah", "berapapun", "berarti", "berawal", "berbagai", "berdatangan", "beri", "berikan", "berikut", "berikutnya", "berjumlah", "berkali-kali", "berkata", "berkehendak", "berkeinginan", "berkenaan", "berlainan", "berlalu", "berlangsung", "berlebihan", "bermacam", "bermacam-macam", "bermaksud", "bermula", "bersama", "bersama-sama", "bersiap", "bersiap-siap", "bertanya", "bertanya-tanya", "berturut", "berturut-turut", "bertutur", "berujar", "berupa", "besar", "betul", "betulkah", "biasa", "biasanya", "bila", "bilakah", "bisa", "bisakah", "boleh", "bolehkah", "bolehlah", "buat", "bukan", "bukankah", "bukanlah", "bukannya", "bulan", "bung", "cara", "caranya", "cukup", "cukupkah", "cukuplah", "cuma", "dahulu", "dalam", "dan", "dapat", "dari", "daripada", "datang", "dekat", "demi", "demikian", "demikianlah", "dengan", "depan", "di", "dia", "diakhiri", "diakhirinya", "dialah", "diantara", "diantaranya", "diberi", "diberikan", "diberikannya", "dibuat", "dibuatnya", "didapat", "didatangkan", "digunakan", "diibaratkan", "diibaratkannya", "diingat", "diingatkan", "diinginkan", "dijawab", "dijelaskan", "dijelaskannya", "dikarenakan", "dikatakan", "dikatakannya", "dikerjakan", "diketahui", "diketahuinya", "dikira", "dilakukan", "dilalui", "dilihat", "dimaksud", "dimaksudkan", "dimaksudkannya", "dimaksudnya", "diminta", "dimintai", "dimisalkan", "dimulai", "dimulailah", "dimulainya", "dimungkinkan", "dini", "dipastikan", "diperbuat", "diperbuatnya", "dipergunakan", "diperkirakan", "diperlihatkan", "diperlukan", "diperlukannya", "dipersoalkan", "dipertanyakan", "dipunyai", "diri", "dirinya", "disampaikan", "disebut", "disebutkan", "disebutkannya", "disini", "disinilah", "ditambahkan", "ditandaskan", "ditanya", "ditanyai", "ditanyakan", "ditegaskan", "ditujukan", "ditunjuk", "ditunjuki", "ditunjukkan", "ditunjukkannya", "ditunjuknya", "dituturkan", "dituturkannya", "diucapkan", "diucapkannya", "diungkapkan", "dong", "dua", "dulu", "empat", "enggak", "enggaknya", "entah", "entahlah", "guna", "gunakan", "hal", "hampir", "hanya", "hanyalah", "hari", "harus", "haruslah", "harusnya", "hendak", "hendaklah", "hendaknya", "hingga", "ia", "ialah", "ibarat", "ibaratkan", "ibaratnya", "ibu", "ikut", "ingat", "ingat-ingat", "ingin", "inginkah", "inginkan", "ini", "inikah", "inilah", "itu", "itukah", "itulah", "jadi", "jadilah", "jadinya", "jangan", "jangankan", "janganlah", "jauh", "jawab", "jawaban", "jawabnya", "jelas", "jelaskan", "jelaslah", "jelasnya", "jika", "jikalau", "juga", "jumlah", "jumlahnya", "justru", "kala", "kalau", "kalaulah", "kalaupun", "kalian", "kami", "kamilah", "kamu", "kamulah", "kan", "kapan", "kapankah", "kapanpun", "karena", "karenanya", "kasus", "kata", "katakan", "katakanlah", "katanya", "ke", "keadaan", "kebetulan", "kecil", "kedua", "keduanya", "keinginan", "kelamaan", "kelihatan", "kelihatannya", "kelima", "keluar", "kembali", "kemudian", "kemungkinan", "kemungkinannya", "kenapa", "kepada", "kepadanya", "kesampaian", "keseluruhan", "keseluruhannya", "keterlaluan", "ketika", "khususnya", "kini", "kinilah", "kira", "kira-kira", "kiranya", "kita", "kitalah", "kok", "kurang", "lagi", "lagian", "lah", "lain", "lainnya", "lalu"
        );
        // cleaning
        $artikel = strtolower($artikel);
        $artikel = preg_replace("/[^a-zA-Z0-9\s]/", "", $artikel);
        $artikel = preg_replace('/\s+/', ' ', $artikel);

        // stopword with satrawati
        $clear = new StopWordRemoverFactory();
        $clear = $clear->createStopWordRemover();
        $artikel = $clear->remove($artikel);
        // $artikel = str_replace($stopwords, "", $artikel);

        return $artikel;
        // steaming sastrawi
        $stemmerFactory = new StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        // $artikel =  $stemmer->stem($artikel);
    }
    public function tokenizer($artikel){
        $token = array();
        $kata = explode(" ", $artikel);
        foreach ($kata as $data) {
            // array_push($token, $stemmer->stem($data));
            $token[] = $data;
        }
        return $token;
    }
}
