<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
error_reporting(false);
header('Content-type: application/json');
$url = $_GET['url'];
$type = $_GET['type'];
$array_type = ["post","story","postkolli","highlight","info"];
if(!in_array($type, $array_type) or !isset($_GET['url']) or !isset($_GET['type'])) {
echo "Use type And url Parameter (url is the username or post url | type is the type of the media)

Available Type Methods :

post -> for photos and videos and igtv and reels
story -> give username to get user stories
info -> give username to get user info
highlight -> give username to get user HighLights
postkolli -> give username to get last user posts";
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
function Getid($username){
$curl = curl_init("https://storiesig.info/api/ig/profile/$username");
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.73 Mobile Safari/537.36');
$ret = curl_exec($curl);
$res = json_decode($ret, true);
$mid = $res['result']['id'];
return $mid;
curl_close($curl);
}
function GetUrl($url){
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL,$url);
curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.73 Mobile Safari/537.36");
curl_setopt($curl,CURLOPT_HEADER,FALSE);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($curl,CURLOPT_TIMEOUT,1);
$output = curl_exec($curl);
return $output;
$outjson = json_encode($output, true);
file_put_contents("tak.json",$outjson);
curl_close($curl);
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
if($type == 'story'){
$id=Getid("$url");
if($id == null){
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',  'Results' =>'wrong id|server problem'], 448);
}else{
$list = json_decode(GetUrl("https://storiesig.info/api/ig/stories/$id"), true);
if($list['result'] == null){
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',  'Results' =>'no story|private account|server problem'], 448);
}else{
for($i=0;$i<=count($list['result'])-1;$i++){
$imageurl= $list['result'][$i]['image_versions2']['candidates'][0]['url'];
$videourl = $list['result'][$i]['video_versions'][0]['url'];
if($videourl == null){
$da =['story' => $imageurl."&dl=1"];
$arrays[] = $da;
}else{
$da = ['story' => $videourl];
$arrays[] = $da;
}
}
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',count =>count($list['result']) ,  'Results' =>$arrays], 448);
}
}
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
if($type == 'postkolli'){
$data['url'] = "https://www.instagram.com/".$url;
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$url= "https://storiesig.info/api/convert";
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.73 Mobile Safari/537.36");
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch,CURLOPT_HEADER, FALSE);
curl_setopt($ch,CURLOPT_NOBODY,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_AUTOREFERER,1);
curl_setopt($ch,CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$res= curl_exec($ch);
curl_close($ch);
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$list = json_decode($res,true);
for($i=0;$i<=count($list)-1;$i++){
$urltype= $list[$i]['url'][0]['type'];
$urlcaption= $list[$i]['meta']['title'];
$urldown= $list[$i]['url'][0]['url'];
$da =['post' => $urldown , 'caption' => $urlcaption , 'type' => $urltype];
$arrays[] = $da;
}
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',count =>count($list) , 'Results' =>$arrays], 448);
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
if($type == 'post'){
$data['url'] = $url;
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$url= "https://storiesig.info/api/convert";
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; Redmi Note 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.73 Mobile Safari/537.36");
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch,CURLOPT_HEADER, FALSE);
curl_setopt($ch,CURLOPT_NOBODY,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_AUTOREFERER,1);
curl_setopt($ch,CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == =
$list = json_decode($res,true);
if($list['url'][0]['type'] == null){
for($i=0;$i<=count($list)-2;$i++){
if($list[$i]['url'][0]['type'] == "jpg"){
$urltype= $list[$i]['url'][0]['type'];
$urlcaption= $list[$i]['meta']['title'];
$urldown= $list[$i]['url'][0]['url'];
$da =['post' => $urldown , 'caption' => $urlcaption , 'type' => $urltype];
$arrays[] = $da;
}else{
$urltype= $list[$i]['url'][0]['type'];
$urlcaption= $list[$i]['meta']['title'];
$urldown= $list[$i]['sd']['url'];
$da =['post' => $urldown , 'caption' => $urlcaption , 'type' => $urltype];
$arrays[] = $da;
}
}
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot','Results' =>$arrays], 448);
}else{
if($list['url'][0]['type'] == "jpg"){
$urltype= $list['url'][0]['type'];
$urlcaption= $list['meta']['title'];
$urldown= $list['url'][0]['url'];
$da =['post' => $urldown , 'caption' => $urlcaption , 'type' => $urltype];
$arrays[] = $da;

echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot','Results' =>$arrays], 448);
}else{
$urltype= $list['url'][0]['type'];
$urlcaption= $list['meta']['title'];
$urldown= $list['sd']['url'];
$da =['post' => $urldown , 'caption' => $urlcaption , 'type' => $urltype];
$arrays[] = $da;
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot','Results' =>$arrays], 448);
}
}
}
//•••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($type == 'highlight'){
$id=Getid("$url");
if($id == null){
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',  'Results' =>'wrong id|server problem'], 448);
}else{
$list = json_decode(GetUrl("https://storiesig.info/api/ig/highlights/$id"), true);
if($list['result'] == null){
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',  'Results' =>'no highlight|server problem'], 448);
}else{
for($i=0;$i<=count($list['result'])-1;$i++){
if (count($arrays) == 10){
break;
}
$urlid= $list['result'][$i]['id'];
$arrays[] = $urlid;
}
foreach ($arrays as $highid) {
$list1 = json_decode(GetUrl("https://storiesig.info/api/ig/highlightStories/$highid"), true);
if(4<=count($list1['result'])){
$ccount=4;
}else{
$ccount=count($list1['result']);
}
for($i=0;$i<= $ccount-1;$i++){
$imageurl = $list1['result'][$i]['image_versions2']['candidates'][0]['url'];
$videourl = $list1['result'][$i]['video_versions'][0]['url'];
if($videourl == null){
$da =['highlight' => $imageurl."&dl=1"];
$arrays1[] = $da;
}else{
$da =['highlight' => $videourl];
$arrays1[] = $da;
}
}
}
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',count =>count($arrays1) ,  'Results' =>$arrays1], 448);
}
}
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
if($type == 'info'){
$array = json_decode(GetUrl("https://storiesig.info/api/ig/profile/$url"), true);
print_r($array);
$id = $get['result']['id'];
$username = $get['result']['username'];
$is_private = $get['result']['is_private'];
$profile_pic_url = $get['result']['profile_pic_url'];
$biography= $get['result']['biography'];
$full_name = $get['result']['full_name'];
$edge_owner_to_timeline_media = $get['result']['edge_owner_to_timeline_media']['count'];
$edge_followed_by = $get['result']['edge_followed_by']['count'];
$edge_follow = $get['result']['edge_follow']['count'];
$arrays = ['id' => $id,'username' => $username,'is_private' => $is_private,'profile_pic_url' => $profile_pic_url,'biography' => $biography,'full_name' => $full_name,'post_count' => $edge_owner_to_timeline_media,'follower_count' => $edge_followed_by,'following_count' => $edge_follow];

// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
echo json_encode(['ok' => true, 'channel' => '@SlowTM','bot' => '@SlowFinderBot',  'Results' =>$arrays], 448);
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 
}
// == == == == == == == == == == == == == == == == == == == == == == == == == == == == = 