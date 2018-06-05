<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use App\Http\Controllers\TwitterController;
use App\Message;

class LinebotController extends Controller
{

    public $file_path_line_log;

    public function __construct()
    {
      $this->file_path_line_log = storage_path().'/logs/line-log.log';
    }

    public function webhook(Request $req){
        //log events
        Log::useFiles($this->file_path_line_log);
        Log::info($req->all());
        $httpClient = new CurlHTTPClient(config('services.botline.access'));
        $bot = new LINEBot($httpClient, [
            'channelSecret' => config('services.botline.secret')
        ]);

        // $signature = $req->header(HTTPHeader::LINE_SIGNATURE);
        // if (empty($signature)) {
        //     abort(401);
        // }
        // try {
        //     $events = $bot->parseEventRequest($req->getContent(), $signature);
        // } catch (\Exception $e) {
        //     logger()->error((string) $e);
        //     abort(200);
        // }

        // foreach ($events as $event) {
        //     switch ($event->getText()) {
        //         case 'Menu Comrades' :
        //             $replyMessage = $this->sendFullMenu($event);
        //             break;
        //         case 'Cek Artikel Terbaru' :
        //             $replyMessage = $this->sendArtikel();
        //             break;
        //         case 'Cek Tweet Komunitas' :
        //             $replyMessage = $this->sendTwitter();
        //             break;
        //         case 'Cari Layanan HIV' :
        //             $replyMessage = $this->sendLokasiARV();
        //             break;
        //         case 'Tweet Dukungan' :
        //             $replyMessage = $this->sendTweetDukungan();
        //             break;
        //         case 'Cek Mitos dan Fakta' :
        //             $replyMessage = $this->sendTweetDukungan();
        //             break;
        //         case 'Menu Konsultasi' :
        //             $replyMessage = $this->sendKonsultasi($event);
        //             break;
        //         case 'Menu Berita' :
        //             $replyMessage = $this->sendBerita();
        //             break;
        //         case 'Menu Artikel' :
        //             $replyMessage = $this->sendArtikel();
        //             break;
        //         case 'Menu Lokasi & Layanan ARV' :
        //             $replyMessage = $this->sendLokasiARV();
        //             break;
        //         case "Cek Artikel Terbaru":
        //             $replyMessage = $this->sendArtikel();
        //             break;
        //         default:
        //             $replyMessage = new TextMessageBuilder('hallo');
        //     }
        //     $this->simpanMessage(["idUser" => $event->getUserId(),"idMessage"=>$event->getMessageId(), "message" => $event->getText()]);
        //     $bot->replyMessage($event->getReplyToken(), $replyMessage);
        // }
        return response('OK', 200);
    }

    public function log() {
      // Log::info('asdasd');
      return response()->file($this->file_path_line_log);
    }

    public function getImageMap($size) {
      $data = file_get_contents(public_path().'\img\FullMenuv2 - '.$size.'.png');
      return response($data)->header('Content-Type', 'image/png');
    }

    public function sendFullMenu($event) {
      $baseSizeBuilder = new BaseSizeBuilder(1800,1040);
      $imagemapMessageActionBuilder1 = new ImagemapMessageActionBuilder(
            'Menu Berita',
            new AreaBuilder(62,338,444,88)
      );
      $imagemapMessageActionBuilder2 = new ImagemapMessageActionBuilder(
            'Menu Artikel',
            new AreaBuilder(537,338,444,88)
      );
      $imagemapMessageActionBuilder3 = new ImagemapMessageActionBuilder(
            'Menu Cerita',
            new AreaBuilder(62,455,444,88)
      );
      $imagemapMessageActionBuilder4 = new ImagemapMessageActionBuilder(
            'Cek Mitos & Fakta',
            new AreaBuilder(537,457,444,88)
      );
      $imagemapMessageActionBuilder5 = new ImagemapMessageActionBuilder(
            'Menu Event',
            new AreaBuilder(62,576,444,88)
      );
      $imagemapMessageActionBuilder6 = new ImagemapMessageActionBuilder(
            'Menu Obat ARV',
            new AreaBuilder(62,848,444,88)
      );
      $imagemapMessageActionBuilder7 = new ImagemapMessageActionBuilder(
            'Menu Konsultasi',
            new AreaBuilder(537,848,444,88)
      );
      $imagemapMessageActionBuilder8 = new ImagemapMessageActionBuilder(
            'Menu Lokasi & Layanan ARV',
            new AreaBuilder(62,964,444,88)
      );
      $imagemapMessageActionBuilder9 = new ImagemapMessageActionBuilder(
            'Menu Tweet Dukungan',
            new AreaBuilder(62,1233,444,88)
      );
      $imagemapMessageActionBuilder10 = new ImagemapMessageActionBuilder(
            'Menu Tweet Komunitas',
            new AreaBuilder(537,1232,444,88)
      );
      $imagemapMessageActionBuilder11 = new ImagemapMessageActionBuilder(
            'Menu Settings',
            new AreaBuilder(62,1499,444,88)
      );
      $imagemapMessageActionBuilder12 = new ImagemapMessageActionBuilder(
            'Menu Profile',
            new AreaBuilder(537,1498,444,88)
      );
      $imagemapMessageActionBuilder13 = new ImagemapMessageActionBuilder(
            'Menu Bantuan',
            new AreaBuilder(62,1619,444,88)
      );
      $ImageMapMessageBuilder = new ImagemapMessageBuilder(
          'https://corachatbot.azurewebsites.net/imgFullMenuV2',
          'Text to be displayed',
          $baseSizeBuilder,
          [
              $imagemapMessageActionBuilder1,
              $imagemapMessageActionBuilder2,
              $imagemapMessageActionBuilder3,
              $imagemapMessageActionBuilder4,
              $imagemapMessageActionBuilder5,
              $imagemapMessageActionBuilder6,
              $imagemapMessageActionBuilder7,
              $imagemapMessageActionBuilder8,
              $imagemapMessageActionBuilder9,
              $imagemapMessageActionBuilder10,
              $imagemapMessageActionBuilder11,
              $imagemapMessageActionBuilder12,
              $imagemapMessageActionBuilder13
          ]
      );

      return $ImageMapMessageBuilder;
    }

    public function sendArtikel() {
      $api = file_get_contents(env('COMRADES_API').'/posting/kategori/Artikel/id/page/0');
      $api = json_decode($api);
      $data = [];

      foreach($api->result as $d) {
        if(substr($d->foto,0,4) == "http") {
            $imageUrl = 'https://corachatbot.azurewebsites.net/img/rumah-cemara.png';
        }else{
            $imageUrl = env('COMRADES_API').'/pic_posting/'.$d->foto;
        }

        $datas = new CarouselColumnTemplateBuilder(substr($d->judul,0,39), substr(strip_tags($d->isi),0,59), $imageUrl, [
          new UriTemplateActionBuilder('Baca lebih lanjut', $d->sumber)
        ]);

        array_push($data, $datas);
      };

      $carouselTemplateBuilder = new CarouselTemplateBuilder($data);
      $messageBuilder = new TemplateMessageBuilder('Artikel Comrades', $carouselTemplateBuilder);

      return $messageBuilder;

      // dd($messageBuilder);
    }
    
    public function sendBerita() {
      $api = file_get_contents(env('COMRADES_API').'/posting/kategori/Berita/id/page/0');
      $api = json_decode($api);
      $data = [];

      foreach($api->result as $d) {
        if(substr($d->foto,0,4) == "http") {
            $imageUrl = 'https'.substr($d->foto,4);
        }else{
            $imageUrl = env('COMRADES_API').'/pic_posting/'.$d->foto;
        }

        $datas = new CarouselColumnTemplateBuilder(substr($d->judul,0,39), substr(strip_tags($d->isi),0,59), $imageUrl, [
          new UriTemplateActionBuilder('Baca lebih lanjut', $d->sumber)
        ]);

        array_push($data, $datas);
      };

      $carouselTemplateBuilder = new CarouselTemplateBuilder($data);
      $messageBuilder = new TemplateMessageBuilder('Berita Comrades', $carouselTemplateBuilder);

      return $messageBuilder;

      // dd($messageBuilder);
    }

    public function sendTwitter() {
      $twitter = new TwitterController();
      $data = [];
      $foto = '';
      foreach ($twitter->getTwitterTimeline() as $value) {
          if($value['user'] == 'RumahCemara') {
            $foto = 'https://corachatbot.azurewebsites.net/img/rumah-cemara.png';
          }else{
            $foto = 'https://corachatbot.azurewebsites.net/img/graha.png';
          };
          $datas = new CarouselColumnTemplateBuilder($value['user'], $value['text'], $foto, [
            new UriTemplateActionBuilder('Go to twitter', 'https://line.me'),
          ]);

          array_push($data, $datas);
      };

      $carouselTemplateBuilder = new CarouselTemplateBuilder($data);

      $messageBuilder = new TemplateMessageBuilder('Twitter Komunitas Graha & Rumah Cemara', $carouselTemplateBuilder);

       return $messageBuilder;
    //   dd($data);
    }

    public function sendLokasiARV() {
      $api = file_get_contents(env('COMRADES_API').'/lokasi_obatLine');
      $api = json_decode($api);
      $data = [];
      $i = 0;

      foreach($api->result as $d) {
        if($i == 9) {
          break;
        };
        $imageUrl = env('COMRADES_API').'/pic_lokasi/'.$d->foto;

        $datas = new CarouselColumnTemplateBuilder(substr($d->nama,0,39), substr(strip_tags($d->deskripsi),0,59), $imageUrl, [
          new UriTemplateActionBuilder('Tunjukan Arah', 'https://www.google.com/maps')
        ]);

        array_push($data, $datas);

        $i++;
      };

      $carouselTemplateBuilder = new CarouselTemplateBuilder($data);
      $messageBuilder = new TemplateMessageBuilder('Lokasi Obat ARV Comrades', $carouselTemplateBuilder);

      return $messageBuilder;
      // dd($messageBuilder);
    }

    public function sendTweetDukungan() {
      $api = file_get_contents(env('COMRADES_API').'/sentiment/0');
      $api = json_decode($api);
      $data = [];

      foreach($api->result as $d) {
        // $imageUrl = env('COMRADES_API').'/pic_posting/'.$d->foto;

        $datas = new CarouselColumnTemplateBuilder(substr($d->screen_name,0,39), substr(strip_tags($d->text),0,59),'', [
          new UriTemplateActionBuilder('Baca lebih lanjut', 'https://twitter.com/'.$d->screen_name.'/status/'.$d->id_string)
        ]);

        array_push($data, $datas);
      };

      $carouselTemplateBuilder = new CarouselTemplateBuilder($data);
      $messageBuilder = new TemplateMessageBuilder('Tweet Dukungan Comrades', $carouselTemplateBuilder);

      return $messageBuilder;
      // dd($messageBuilder);
    }

    public function simpanMessage($data) {
      try {
        $data = new Message($data);
        $data->save();

        // return response()->json(["status"=>200,"message"=>"berhasil"]);
      }catch(Exception $e) {
        logger()->error((string) $e);
      }

    }

    public function sendKonsultasi($event) {
        return $replyMessage = new TextMessageBuilder('Maaf layanan yang anda pilih belum tersedia.');
    }
}
