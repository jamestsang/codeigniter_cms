<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

     if ( ! function_exists('asset_url'))
     {
       function asset_url($more = "")
       {
          return base_url().'assets/'.$more;
       }
     }

     if ( ! function_exists('asset_raw_path'))
     {
       function asset_raw_path($more = "")
       {
          return "/".BASE_FOLDER.'assets/'.$more;
       }
     }
     
     if ( ! function_exists('clearStr'))
     {
       function clearStr($input)
       {
           $output = htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
           return $output;
        }
     }

     if( ! function_exists('truncate')){
          function truncate($number, $places) { 
            $power = pow(10, $places); 
            return floor($number * $power) / $power; 
          } 

     }

     if( ! function_exists('langc')){
          function langc($key) { 
            if(lang($key) != ""){
              return lang($key);
            } else{
              return $key;
            }
          } 

     }

     function shareBtn(){
      return '<div id="fb-root"></div>
          <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/zh_HK/sdk.js#xfbml=1&version=v3.0&appId={appid}&autoLogAppEvents=1";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, \'script\', \'facebook-jssdk\'));</script>

          <!-- Your share button code -->
          <div class="fb-share-button" 
            data-href="'.base_current_url().'" 
            data-layout="button_count">
          </div>';
     }
     
     