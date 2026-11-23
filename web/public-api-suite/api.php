<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
// T.me/PejvakSource | سورس ربات تلگرام ، پژواک سورس

منبع سورس : دیمو تیم
DevUltra

// T.me/PejvakSource | سورس ربات تلگرام ، پژواک سورس
*/

header('Content-Type: Application/Json');
error_reporting(0);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://www.digikala.com/search/?q=" . urlencode($_GET['q']));
curl_setopt($curl, CURLOPT_POST, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);

preg_match_all('#data-enhanced-ecommerce=\'{"id":(.*),"name":"(.*?)","category":"(.*?)","brand":"(.*?)","variant":.*,"price":(.*?),"quantity":.*}\'><div#', $result, $info);
preg_match_all('#<img src="(.*?)?x-oss-process=image/resize,m_lfit,h_600,w_600/quality,q_90"#', $result, $images);

if($_GET['q'] == null or $_GET['q'] == "" or !isset($_GET['q'])){
    
    $array = [
        'ok' => false,
        'channel' => 'T.me/PejvakSource',
        'description' => "'q' is null"
    ];
       
}else{

    $array = [
        'ok' => true,
        'channel' => 'T.me/PejvakSource',
        'count' => count($info[1])
    ];
    
    foreach($info[2] as $name){
        $in++;
        $array['result'][$in]['name'] = $name;
    }
    
    foreach($info[3] as $category){
        $ic++;
        $array['result'][$ic]['category'] = $category;
    }
    
    foreach($info[4] as $brand){
        $ib++;
        $array['result'][$ib]['brand'] = $brand;
    }
    
    foreach($info[5] as $price){
        $ip++;
        $array['result'][$ip]['price'] = number_format($price / 10) . " تومان";
    }
    
    foreach($info[1] as $id){
        $iid++;
        $array['result'][$iid]['id'] = $id;
        $array['result'][$iid]['product_link'] = 'https://www.digikala.com/product/dkp-' . $id;
    }
    
    foreach($images[1] as $image){
        $ii++;
        if($array['result'][$ii]['name']) $array['result'][$ii]['image'] = $image;
    }

}

echo json_encode($array, 384);

/*
// T.me/PejvakSource | سورس ربات تلگرام ، پژواک سورس

منبع سورس : دیمو تیم
DevUltra

// T.me/PejvakSource | سورس ربات تلگرام ، پژواک سورس
*/

?>