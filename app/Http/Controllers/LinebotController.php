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

        $signature = $req->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            abort(401);
        }
        try {
            $events = $bot->parseEventRequest($req->getContent(), $signature);
        } catch (\Exception $e) {
            logger()->error((string) $e);
            abort(200);
        }

        foreach ($events as $event) {
            $replyMessage = new TextMessageBuilder('hallo');
            $arrMenu = [
              'Menu Comrades' => $this->sendFullMenu($event),
              'Cek Artikel Terbaru' => $this->sendArtikel(),
              'Cek Tweet Komunitas' => $this->sendTwitter(),
              'Cari Layanan HIV' => $this->sendLokasiARV(),
              'Tweet Dukungan' => $this->sendTweetDukungan(),
              'Cek Mitos dan Fakta' => $this->sendTweetDukungan()
            ];
            
            if (array_key_exists($event->getText(), $arrMenu)) {
              $replyMessage = $arrMenu[$event->getText()];
            }

            if(substr($event->getText(), -1) == '?') {
              $replyMessage = $this->sendTwitter();
            };

            $bot->replyMessage($event->getReplyToken(), $replyMessage);
        }
        return response('OK', 200);
    }

    public function log() {
      // Log::info('asdasd');
      return response()->file($this->file_path_line_log);
    }

    public function getImageMap($size) {
      $data = file_get_contents(public_path().'\img\FullMenu - '.$size.'.png');
      return response($data)->header('Content-Type', 'image/png');
    }

    public function sendFullMenu($event) {
      $baseSizeBuilder = new BaseSizeBuilder(1040,1040);
      $imagemapMessageActionBuilder1 = new ImagemapMessageActionBuilder(
            'Menu Berita',
            new AreaBuilder(30,132,464,94)
      );
      $imagemapMessageActionBuilder2 = new ImagemapMessageActionBuilder(
            'Menu Artikel',
            new AreaBuilder(542,132,464,94)
      );
      $ImageMapMessageBuilder = new ImagemapMessageBuilder(
          'https://corachatbot.azurewebsites.net/imgFullMenu',
          'Text to be displayed',
          $baseSizeBuilder,
          [
              $imagemapMessageActionBuilder1,
              $imagemapMessageActionBuilder2
          ]
      );

      return $ImageMapMessageBuilder;
    }

    public function sendArtikel() {
      $api = file_get_contents(env('COMRADES_API').'/posting/kategori/Artikel/id/page/0');
      $api = json_decode($api);
      $data = [];

      foreach($api->result as $d) {
        $imageUrl = env('COMRADES_API').'/pic_posting/'.$d->foto;

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
}
