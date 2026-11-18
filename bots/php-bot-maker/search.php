<?php
// Telegram: @Abolfazl_rwm
  /***********
IR Creator
  *********/
  $text = isset ($_GET ['search']) ? $_GET ['search'] : $_POST ['search'];
  $type = isset ($_GET ['type']) ? $_GET ['type'] : $_POST ['type'];
  $username = str_replace ('@', '', isset ($_GET ['username']) ? $_GET ['username'] : $_POST ['username']); 
  function search_post($username , $text){
$post = array();
$hh = file_get_contents("https://telegram.me/s/$username/?q=".urlencode($text));
preg_match_all('#<time datetime="(.*?)T([0-9-:]+)(.*?)">(.*?)</time>#', $hh , $date);
        unset($date['0']);
  preg_match_all('#<a class="tgme_widget_message_date" href="https://t.me/(.*?)/(.*?)">#', $hh , $message_id);
         unset($message_id['1']);
         unset($message_id['0']);
         $message_id = $message_id['2'];
     foreach($message_id as $i => $message_id){
  $post[ $i]['date'][] = strip_tags(urldecode($date[1][$i]));
$post[$i]['date'][] = strip_tags(urldecode($date[2][$i]));
  $post[ $i]['channel'] = $username;
    $post[$i]['message_id'] = $message_id;
  }
  return array_reverse($post);
   }
$xml = new SimpleXMLElement('<rootTag/>'); 
    if ($username != '' && $text != ''){  $a = search_post ($username , $text);
  if (count ($a) <= 0){
    if (preg_match('/^[Xx][Mm][Ll]$/', $type)){
header('Content-Type: application/xml');
to_xml($xml, ['ok'=>false, 'result'=>null , 'count_result'=>0]);
echo $xml->asXML ();
    }else{
  header('Content-Type: application/json');
echo json_encode(['ok'=>false, 'result'=>null , 'count_result'=>0] ,JSON_PRETTY_PRINT );
  }
 }else {
 $a = search_post( $username , $text);
 if(preg_match('/^[Xx][Mm][Ll]$/', $type)){
header('Content-Type: application/xml');
to_xml($xml,['ok'=>true, 'result'=>$a , 'count_result'=>count ( $a)]);
echo $xml->asXML ();
    }else{
  header('Content-Type: application/json');
 echo json_encode(['ok'=>true, 'result'=>$a , 'count_result'=>count ( $a)] ,JSON_PRETTY_PRINT );
     }
  }
   }else {
 if (preg_match('/^[Xx][Mm][Ll]$/', $type)){
header('Content-Type: application/xml');
to_xml($xml, ['ok'=>false, 'result'=>null , 'count_result'=>0]);
echo $xml->asXML ();
    }else{
  header('Content-Type: application/json');
  echo json_encode (['ok'=>false, 'result'=>null , 'count_result'=>0] , JSON_PRETTY_PRINT);
  }
 }
function to_xml(SimpleXMLElement $object, $data , $a2=null) { 
  foreach ($data as $key => $value) {
   if (is_array($value)) {
     if (!preg_match ( '/^([0-9]+)$/', $key)){
  $is_ii = 0;
    foreach ( $value as $im => $vl){
    if ( preg_match ( '/^([0-9]+)$/', $im)){
    $is_ii=1;
   }
  }
   if (!$is_ii){
   $new_object = $object->addChild($key);
    to_xml($new_object, $value , $key); 
   }else {
  to_xml($object, $value , $key); 
   }
      }else{
  $new_object = $object->addChild($a2); to_xml($new_object, $value); 
     }
   } else { 
    if ( is_bool ( $value ) && !is_int ( $value ) ){
       if ( $value ){
     $value = 'true';
    }else {
  $value = 'false';
  }
}
   if (preg_match ( '/^([0-9]+)$/', $key)) { 
     $object->addChild($a2, $value); 
  } else {
   $object->addChild($key, $value); 
      }
    } 
  }
} 
?>
