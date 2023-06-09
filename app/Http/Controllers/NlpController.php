<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hoax;
use Illuminate\Support\Arr;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


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

        $tmp[] = $this->cosine_similarity($token1, $token3);
        $tmp[] = $this->jaccard_similarity($token1, $token3);
        $tmp[] = $this->euclidean_distance($token1, $token3);

        // $script = shell_exec('python D:\TugasAkhir\public\script\basic.py \"'.json_encode($token1).'\"'); // input\"{$text}\"");
        // dd($script);
        
        dd($this->tf_idf($token1));

        similar_text($artikel1, $artikel3, $percent);
        // dd($token1);
        // dd($this->cosine_similarity($token1, $token2));
        // dd($this->tf_idf($token1));
        // dd($this->jaccard_similarity($token1, $token2));
        return view('Cek');
    }

    public function cek(Request $request){
        $berita = $this->tokenizer($this->preprocessing($request->berita));
        $hoax = Hoax::all();
        $hasil = null;
        $log = array();
        
        // do check
        foreach($hoax as $index => $data){
            $tmp = $this->jaccard_similarity($berita, json_decode($data->hoax));
            $log[]= $tmp;
            if($hasil === null || $hasil < $tmp){
                $hasil = $tmp;
            }
        }

        if($tmp >= 1){
            $hasil = 100;
        }else{
            $hasil = round($tmp*100);
        }

        dd($hasil);
        
        return view('Hasil',[
            'berita' =>$request->berita,
            'hoax' => $hasil,
            'asli' => (100 - $hasil),
        ]);
    }
    public function hoax(){
        return view('Hoax');
    }
    public function corpus(){
        $hoax = Hoax::all();
        $corpus = [];
        foreach($hoax as $data){
            $corpus[] = json_decode($data->hoax);
        }
        return $corpus;
    }
    public function simpan(Request $request){
        $berita = $this->tokenizer($this->preprocessing($request->berita));
        $hoax = new Hoax();
        $hoax->hoax = json_encode($berita);
        $hoax->save();
        return redirect('/hoax')->with('msg', 'Berhasil Simpan');;
    }

    public function cosine_similarity($utama, $pembanding){
        $a = 0;
        $b = 0;
        $ab = 0;

        $set =  array_unique($utama);
        $tmp = array_count_values($utama);
        $tmp1 = array_count_values($pembanding);
        
        // bow
        foreach($set as $raw){
            $a += pow($tmp[$raw], 2);
            if(isset($tmp1[$raw])){
                $b += pow($tmp1[$raw],2);
                $ab += ($tmp[$raw] * $tmp1[$raw]);
            }else{
                $b += pow(0,2);
                $ab += 0;
            }
        }
        return $ab / (abs(sqrt($a)) * abs(sqrt($b)));
    }
    public function euclidean_distance($utama, $pembanding){
        $qp = 0;

        $set =  array_unique($utama);
        $tmp = array_count_values($utama);
        $tmp1 = array_count_values($pembanding);
        
        foreach($set as $raw){
            // $kata[0][] = $tmp[$raw];
            if(isset($tmp1[$raw])){
               $qp += pow(($tmp[$raw] - $tmp1[$raw]), 2);
                // $kata[1][] = $tmp1[$raw];
            }else{
               $qp += pow(($tmp[$raw] - 0), 2);
                // $kata[1][] = 0;
            }
        }
        return sqrt($qp);
    }
    public function array_set($utama){
        $tmp = [];
        foreach($utama as $word){
            $tmp += [$word => 0];
        }
        return $tmp;
    }
    public function tf_idf($utama)
    {
        $corpus = $this->corpus();

        // vect 1
        $vect1 = array('tf' => $this->do_tf([$utama]), 'idf' => $this->do_idf([$utama]));
        $tfIdf1 = $this->array_set($utama);
        $tmp = [];
        foreach($utama as $doc => $val1){
            if(in_array($val1, $utama)){
                $tfidf = $vect1['tf'][0][$val1] *  $vect1['idf'][$val1];
                $tfIdf1[$val1]= $tfidf;
            }
        }


        // vect 2
        $vect2 = array('tf' => $this->do_tf($corpus), 'idf' => $this->do_idf($corpus));
        $tfIdf2 = [];
        foreach($corpus as $doc => $val1){
            $tmp = $this->array_set($utama);
            foreach($val1 as $word => $val2){
                if(in_array($val2, $utama)){
                    $tfidf = $vect2['tf'][$doc][$val2] *  $vect2['idf'][$val2];
                    $tmp[$val2]= $tfidf;
                }
            }
            $tfIdf2[] = $tmp;
        }

        // cosien

        $cos = [];
        foreach($tfIdf2 as $doc => $val1){
            $a = 0;
            $b = 0;
            $c = 0;
            foreach($val1 as $term => $val2){
                $a += ($val2 * $tfIdf1[$term]);
                $b += pow($tfIdf1[$term], 2);
                $c += pow($val2, 2);
            }
            $cos[] = $a / sqrt($b) * sqrt($c);
        }
        $tmp = [];
        $tmp[] = $cos;
        $tmp[] = $tfIdf2;
        $tmp[] = $tfIdf1;
        return $vect1;

    }
    public function do_tf($documents){
        $tf = [];
            foreach($documents as $doc => $val1){
                $tmp = [];
                foreach($val1 as $word => $val2){
                    $tmp += [$val2 =>  $this->tf($val2, $val1)];          
                }
                $tf[] = $tmp;
            }
        return $tf;
    }
    public function tf($text, $doc){
        $tf = 0;
        foreach($doc as $word){
            if($text == $word){
                $tf += 1;
            }
        }
        return $tf / count($doc);
    }
    public function do_idf($documents){
        $idf = [];
        foreach($documents as $doc => $val1){
            foreach($val1 as $word => $val2){
                if(!isset($idf[$val2])){
                    $idf += [$val2 =>  $this->idf($val2)];
                }
            }
        }
        return $idf;
    }
    public function idf($text){
        $numTerms = 0;
        $corpus = $this->corpus();

        foreach($corpus as $doc => $val1){
            if (in_array($text, $val1))
            {
                $numTerms++;
            }
        }
        if($numTerms > 0){
            return 1 + log(count($corpus)/$numTerms);
        }else{
            return 1;
        }
    }

    public function jaccard_similarity($utama, $pembanding) {
        $jmlKataSama = count(array_intersect($utama, $pembanding));
        $jmlGabunganKata = count(array_unique(array_merge($utama, $pembanding)));
        return $jmlKataSama/ $jmlGabunganKata;
    }

    public function preprocessing($artikel){
        // $stopwords = array(
        //     "ada", "adalah", "adanya", "adapun", "agak", "agaknya", "agar", "akan", "akankah", "akhir", "akhiri", "akhirnya", "aku", "akulah", "amat", "amatlah", "anda", "andalah", "antar", "antara", "antaranya", "apa", "apaan", "apabila", "apakah", "apalagi", "apatah", "artinya", "asal", "asalkan", "atas", "atau", "ataukah", "ataupun", "awal", "awalnya", "bagai", "bagaikan", "bagaimana", "bagaimanakah", "bagaimanapun", "bagi", "bagian", "bahkan", "bahwa", "bahwasanya", "baik", "bakal", "bakalan", "balik", "banyak", "bapak", "baru", "bawah", "beberapa", "begini", "beginian", "beginikah", "beginilah", "begitu", "begitukah", "begitulah", "begitupun", "bekerja", "belakang", "belakangan", "belum", "belumlah", "benar", "benarkah", "benarlah", "berada", "berakhir", "berakhirlah", "berakhirnya", "berapa", "berapakah", "berapalah", "berapapun", "berarti", "berawal", "berbagai", "berdatangan", "beri", "berikan", "berikut", "berikutnya", "berjumlah", "berkali-kali", "berkata", "berkehendak", "berkeinginan", "berkenaan", "berlainan", "berlalu", "berlangsung", "berlebihan", "bermacam", "bermacam-macam", "bermaksud", "bermula", "bersama", "bersama-sama", "bersiap", "bersiap-siap", "bertanya", "bertanya-tanya", "berturut", "berturut-turut", "bertutur", "berujar", "berupa", "besar", "betul", "betulkah", "biasa", "biasanya", "bila", "bilakah", "bisa", "bisakah", "boleh", "bolehkah", "bolehlah", "buat", "bukan", "bukankah", "bukanlah", "bukannya", "bulan", "bung", "cara", "caranya", "cukup", "cukupkah", "cukuplah", "cuma", "dahulu", "dalam", "dan", "dapat", "dari", "daripada", "datang", "dekat", "demi", "demikian", "demikianlah", "dengan", "depan", "di", "dia", "diakhiri", "diakhirinya", "dialah", "diantara", "diantaranya", "diberi", "diberikan", "diberikannya", "dibuat", "dibuatnya", "didapat", "didatangkan", "digunakan", "diibaratkan", "diibaratkannya", "diingat", "diingatkan", "diinginkan", "dijawab", "dijelaskan", "dijelaskannya", "dikarenakan", "dikatakan", "dikatakannya", "dikerjakan", "diketahui", "diketahuinya", "dikira", "dilakukan", "dilalui", "dilihat", "dimaksud", "dimaksudkan", "dimaksudkannya", "dimaksudnya", "diminta", "dimintai", "dimisalkan", "dimulai", "dimulailah", "dimulainya", "dimungkinkan", "dini", "dipastikan", "diperbuat", "diperbuatnya", "dipergunakan", "diperkirakan", "diperlihatkan", "diperlukan", "diperlukannya", "dipersoalkan", "dipertanyakan", "dipunyai", "diri", "dirinya", "disampaikan", "disebut", "disebutkan", "disebutkannya", "disini", "disinilah", "ditambahkan", "ditandaskan", "ditanya", "ditanyai", "ditanyakan", "ditegaskan", "ditujukan", "ditunjuk", "ditunjuki", "ditunjukkan", "ditunjukkannya", "ditunjuknya", "dituturkan", "dituturkannya", "diucapkan", "diucapkannya", "diungkapkan", "dong", "dua", "dulu", "empat", "enggak", "enggaknya", "entah", "entahlah", "guna", "gunakan", "hal", "hampir", "hanya", "hanyalah", "hari", "harus", "haruslah", "harusnya", "hendak", "hendaklah", "hendaknya", "hingga", "ia", "ialah", "ibarat", "ibaratkan", "ibaratnya", "ibu", "ikut", "ingat", "ingat-ingat", "ingin", "inginkah", "inginkan", "ini", "inikah", "inilah", "itu", "itukah", "itulah", "jadi", "jadilah", "jadinya", "jangan", "jangankan", "janganlah", "jauh", "jawab", "jawaban", "jawabnya", "jelas", "jelaskan", "jelaslah", "jelasnya", "jika", "jikalau", "juga", "jumlah", "jumlahnya", "justru", "kala", "kalau", "kalaulah", "kalaupun", "kalian", "kami", "kamilah", "kamu", "kamulah", "kan", "kapan", "kapankah", "kapanpun", "karena", "karenanya", "kasus", "kata", "katakan", "katakanlah", "katanya", "ke", "keadaan", "kebetulan", "kecil", "kedua", "keduanya", "keinginan", "kelamaan", "kelihatan", "kelihatannya", "kelima", "keluar", "kembali", "kemudian", "kemungkinan", "kemungkinannya", "kenapa", "kepada", "kepadanya", "kesampaian", "keseluruhan", "keseluruhannya", "keterlaluan", "ketika", "khususnya", "kini", "kinilah", "kira", "kira-kira", "kiranya", "kita", "kitalah", "kok", "kurang", "lagi", "lagian", "lah", "lain", "lainnya", "lalu"
        // );
        // cleaning
        $artikel = strtolower($artikel);
        $artikel = preg_replace("/[^a-zA-Z0-9\s]/", "", $artikel);
        $artikel = preg_replace("/\d+/u", "", $artikel);
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
            if($data === ''){continue;}
            // array_push($token, $stemmer->stem($data));
            $token[] = $data;
        }
        return $token;
    }
}
