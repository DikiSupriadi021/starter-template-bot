<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twitter;

class TwitterController extends Controller
{
    public function getGrahaTimeline() {
      try
      {
      	$response = Twitter::getUserTimeline(['screen_name' => 'grahapitamerah', 'count' => 20, 'format' => 'json']);
      }
      catch (Exception $e)
      {
      	// dd(Twitter::error());
      	dd(Twitter::logs());
      }

      return $response;
    }
}
