<?php
//####################################################################
$Button_CH = json_encode(['keyboard'=>[
[['text'=>'📟 کانال تبلیغات']],
[['text'=>'📟 کانال جوین اجباری دوم'],['text'=>'📟 کانال جوین اجباری اول']],
[['text'=>'🔙 برگشت']],
],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\
$Button_Join = json_encode(['inline_keyboard'=>[
[['text'=>"✅تایید عضویت",'callback_data'=>'join']],
]]);
//####################################################################
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok7.txt")){
$dok7 = file_get_contents("Button/dok7.txt");
}else{
$dok7 = "جمع آوری سکه رایگان |💰|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok2.txt")){
$dok2 = file_get_contents("Button/dok2.txt");
}else{
$dok2 = "حساب کاربری |🔐|";
}
if(file_exists("Button/dok1.txt")){
$dok1 = file_get_contents("Button/dok1.txt");
}else{
$dok1 = "سفارش بازدید |👁‍🗨|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok3.txt")){
$dok3 = file_get_contents("Button/dok3.txt");
}else{
$dok3 = "دعوت دیگران |⚡️|";
}
if(file_exists("Button/dok12.txt")){
$dok12 = file_get_contents("Button/dok12.txt");
}else{
$dok12 = "فروشگاه |🛍|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok4.txt")){
$dok4 = file_get_contents("Button/dok4.txt");
}else{
$dok4 = "انتقال |🏧|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok0.txt")){
$dok0 = file_get_contents("Button/dok0.txt");
}else{
$dok0 = "کد هدیه |🎉|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok9.txt")){
$dok9 = file_get_contents("Button/dok9.txt");
}else{
$dok9 = "ربات نمایندگی |⚙️|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok8.txt")){
$dok8 = file_get_contents("Button/dok8.txt");
}else{
$dok8 = "برترین ها |🏆|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok6.txt")){
$dok6 = file_get_contents("Button/dok6.txt");
}else{
$dok6 = "پشتیبانی |📮|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok11.txt")){
$dok11 = file_get_contents("Button/dok11.txt");
}else{
$dok11 = "پیگیری‌ سفارش |🔍|";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dok5.txt")){
$dok5 = file_get_contents("Button/dok5.txt");
}else{
$dok5 = "قوانین |⚖️|";
}
//########################   تعداد بازدید ثبت سفارش   ################################
if(file_exists("Button/dokk1.txt")){
$dokk1 = file_get_contents("Button/dokk1.txt");
}else{
$dokk1 = "10";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk2.txt")){
$dokk2 = file_get_contents("Button/dokk2.txt");
}else{
$dokk2 = "20";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk3.txt")){
$dokk3 = file_get_contents("Button/dokk3.txt");
}else{
$dokk3 = "30";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk4.txt")){
$dokk4 = file_get_contents("Button/dokk4.txt");
}else{
$dokk4 = "40";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk5.txt")){
$dokk5 = file_get_contents("Button/dokk5.txt");
}else{
$dokk5 = "50";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk6.txt")){
$dokk6 = file_get_contents("Button/dokk6.txt");
}else{
$dokk6 = "80";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk7.txt")){
$dokk7 = file_get_contents("Button/dokk7.txt");
}else{
$dokk7 = "120";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk8.txt")){
$dokk8 = file_get_contents("Button/dokk8.txt");
}else{
$dokk8 = "160";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk9.txt")){
$dokk9 = file_get_contents("Button/dokk9.txt");
}else{
$dokk9 = "220";
}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/dokk10.txt")){
$dokk10 = file_get_contents("Button/dokk10.txt");
}else{
$dokk10 = "280";
}
//////////------------------------\\\\\\\\\\\\\\
//########################  تعداد سکه ثبت سفارش  ##################################
//----------------------------------------/////
if(file_exists("Button/dokc1.txt")){
$dokc1 = file_get_contents("Button/dokc1.txt");
}else{
$dokc1 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc2.txt")){
$dokc2 = file_get_contents("Button/dokc2.txt");
}else{
$dokc2 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc3.txt")){
$dokc3 = file_get_contents("Button/dokc3.txt");
}else{
$dokc3 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc4.txt")){
$dokc4 = file_get_contents("Button/dokc4.txt");
}else{
$dokc4 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc5.txt")){
$dokc5 = file_get_contents("Button/dokc5.txt");
}else{
$dokc5 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc6.txt")){
$dokc6 = file_get_contents("Button/dokc6.txt");
}else{
$dokc6 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc7.txt")){
$dokc7 = file_get_contents("Button/dokc7.txt");
}else{
$dokc7 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc8.txt")){
$dokc8 = file_get_contents("Button/dokc8.txt");
}else{
$dokc8 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc9.txt")){
$dokc9 = file_get_contents("Button/dokc9.txt");
}else{
$dokc9 = "1";
}
//----------------------------------------/////
if(file_exists("Button/dokc10.txt")){
$dokc10 = file_get_contents("Button/dokc10.txt");
}else{
$dokc10 = "1";
}
//----------------------------------------/////
//############################   نام آیتم   ############################
//----------------------------------------/////
if(file_exists("Button/dokt1.txt")){
$dokt1 = file_get_contents("Button/dokt1.txt");
}else{
$dokt1 = "👁‍🗨 $dokk1 بازدید | 💰 $dokc1 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt2.txt")){
$dokt2 = file_get_contents("Button/dokt2.txt");
}else{
$dokt2 = "👁‍🗨 $dokk2 بازدید | 💰 $dokc2 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt3.txt")){
$dokt3 = file_get_contents("Button/dokt3.txt");
}else{
$dokt3 = "👁‍🗨 $dokk3 بازدید | 💰 $dokc3 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt4.txt")){
$dokt4 = file_get_contents("Button/dokt4.txt");
}else{
$dokt4 = "👁‍🗨 $dokk4 بازدید | 💰 $dokc4 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt5.txt")){
$dokt5 = file_get_contents("Button/dokt5.txt");
}else{
$dokt5 = "👁‍🗨 $dokk5 بازدید | 💰 $dokc5 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt6.txt")){
$dokt6 = file_get_contents("Button/dokt6.txt");
}else{
$dokt6 = "👁‍🗨 $dokk6 بازدید | 💰 $dokc6 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt7.txt")){
$dokt7 = file_get_contents("Button/dokt7.txt");
}else{
$dokt7 = "👁‍🗨 $dokk7 بازدید | 💰 $dokc7 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt8.txt")){
$dokt8 = file_get_contents("Button/dokt8.txt");
}else{
$dokt8 = "👁‍🗨 $dokk8 بازدید | 💰 $dokc8 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt9.txt")){
$dokt9 = file_get_contents("Button/dokt9.txt");
}else{
$dokt9 = "👁‍🗨 $dokk9 بازدید | 💰 $dokc9 سکه";
}
//----------------------------------------/////
if(file_exists("Button/dokt10.txt")){
$dokt10 = file_get_contents("Button/dokt10.txt");
}else{
$dokt10 = "👁‍🗨 $dokk10 بازدید | 💰 $dokc10 سکه";
}
//####################################################################
//----------------------------------------/////
if(file_exists("Button/starts.txt")){
$starts = file_get_contents("Button/starts.txt");
}else{
$starts = "0";
}
//----------------------------------------/////
if(file_exists("Button/Diamond.txt")){
$Diamond = file_get_contents("Button/Diamond.txt");
}else{
$Diamond = "0";
}
//----------------------------------------/////
if(file_exists("Button/delmin.txt")){
$delmin = file_get_contents("Button/delmin.txt");
}else{
$delmin = "10";
}
//----------------------------------------/////
if(file_exists("Button/delmax.txt")){
$delmax = file_get_contents("Button/delmax.txt");
}else{
$delmax = "500";
}
//----------------------------------------/////
if(file_exists("Button/zirmjmaec.txt")){
$zirmjmaec = file_get_contents("Button/zirmjmaec.txt");
}else{
$zirmjmaec = "1";
}
//----------------------------------------/////
if(file_exists("Button/zirDiamonds.txt")){
$zirDiamonds = file_get_contents("Button/zirDiamonds.txt");
}else{
$zirDiamonds = "1";
}
//----------------------------------------/////
//####################################################################
//----------------------------------------/////
if(file_exists("Button/start.txt")){
$start = file_get_contents("Button/start.txt");
}else{
$start = "متن پیش فرض - قابل تنظیم از مدیریت";
}
//----------------------------------------/////
if(file_exists("Button/doktxt1.txt")){
$doktxt1 = file_get_contents("Button/doktxt1.txt");
}else{
$doktxt1 = "✅ لطفا نوع بازدید خود را انتخاب کنید";
}
//----------------------------------------/////
if(file_exists("Button/doktxt3.txt")){
$doktxt3 = file_get_contents("Button/doktxt3.txt");
}else{
$doktxt3 = "متن پیش فرض - قابل تنظیم از مدیریت";
}
//----------------------------------------/////
if(file_exists("Button/doktxt4.txt")){
$doktxt4 = file_get_contents("Button/doktxt4.txt");
}else{
$doktxt4 = "↗️ لطفا در خط اول شناسه کاربری فرد و در خط دوم تعداد سکه را وارد کنید
🎫 شناسه کاربری هر فرد در قسمت حساب من درج شده است  .

ℹ️ مثال :
123456789 
10";
}
//----------------------------------------/////
if(file_exists("Button/doktxt5.txt")){
$doktxt5 = file_get_contents("Button/doktxt5.txt");
}else{
$doktxt5 = "متن پیش فرض - قابل تنظیم از مدیریت";
}
//----------------------------------------/////
if(file_exists("Button/doktxt6.txt")){
$doktxt6 = file_get_contents("Button/doktxt6.txt");
}else{
$doktxt6 = "متن پیش فرض - قابل تنظیم از مدیریت";
}
//----------------------------------------/////
if(file_exists("Button/doktxt7.txt")){
$doktxt7 = file_get_contents("Button/doktxt7.txt");
}else{
$doktxt7 = "متن پیش فرض - قابل تنظیم از مدیریت";
}
//----------------------------------------/////
if(file_exists("Button/doktxt8.txt")){
$doktxt8 = file_get_contents("Button/doktxt8.txt");
}else{
$doktxt8 = "تمایل به مشاهده برترین کاربران کدام بخش دارید؟";
}
//----------------------------------------/////
if(file_exists("Button/doktxt9.txt")){
$doktxt9 = file_get_contents("Button/doktxt9.txt");
}else{
$doktxt9 = "🔹 گزینه مورد نظر را انتخاب کنید :";
}
//----------------------------------------/////
if(file_exists("Button/doktxt12.txt")){
$doktxt12 = file_get_contents("Button/doktxt12.txt");
}else{
$doktxt12 = "متن پیش فرض - قابل تنظیم از مدیریت";
}


//----------------------------------------/////
//####################################################################
if(file_exists("keyboard/home/line11.txt")){
$line1_1 = file_get_contents("keyboard/home/line11.txt");
if($line1_1 != null ){
$line1_1 = str_replace('DOK1',$dok1,$line1_1);
$line1_1 = str_replace('DOK2',$dok2,$line1_1);
$line1_1 = str_replace('DOK3',$dok3,$line1_1);
$line1_1 = str_replace('DOK4',$dok4,$line1_1);
$line1_1 = str_replace('DOK5',$dok5,$line1_1);
$line1_1 = str_replace('DOK6',$dok6,$line1_1);
$line1_1 = str_replace('DOK7',$dok7,$line1_1);
$line1_1 = str_replace('DOK8',$dok8,$line1_1);
$line1_1 = str_replace('DOK9',$dok9,$line1_1);
$line1_1 = str_replace('DOK0',$dok0,$line1_1);
$line1_1 = str_replace('DUQ1',$dok11,$line1_1);
$line1_1 = str_replace('DUQ2',$dok12,$line1_1);
}else{
$line1_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line12.txt")){
$line1_2 = file_get_contents("keyboard/home/line12.txt");
if($line1_2 != null ){
$line1_2 = str_replace('DOK1',$dok1,$line1_2);
$line1_2 = str_replace('DOK2',$dok2,$line1_2);
$line1_2 = str_replace('DOK3',$dok3,$line1_2);
$line1_2 = str_replace('DOK4',$dok4,$line1_2);
$line1_2 = str_replace('DOK5',$dok5,$line1_2);
$line1_2 = str_replace('DOK6',$dok6,$line1_2);
$line1_2 = str_replace('DOK7',$dok7,$line1_2);
$line1_2 = str_replace('DOK8',$dok8,$line1_2);
$line1_2 = str_replace('DOK9',$dok9,$line1_2);
$line1_2 = str_replace('DOK0',$dok0,$line1_2);
$line1_2 = str_replace('DUQ1',$dok11,$line1_2);
$line1_2 = str_replace('DUQ2',$dok12,$line1_2);
}else{
$line1_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line13.txt")){
$line1_3 = file_get_contents("keyboard/home/line13.txt");
if($line1_3 != null ){
$line1_3 = str_replace('DOK1',$dok1,$line1_3);
$line1_3 = str_replace('DOK2',$dok2,$line1_3);
$line1_3 = str_replace('DOK3',$dok3,$line1_3);
$line1_3 = str_replace('DOK4',$dok4,$line1_3);
$line1_3 = str_replace('DOK5',$dok5,$line1_3);
$line1_3 = str_replace('DOK6',$dok6,$line1_3);
$line1_3 = str_replace('DOK7',$dok7,$line1_3);
$line1_3 = str_replace('DOK8',$dok8,$line1_3);
$line1_3 = str_replace('DOK9',$dok9,$line1_3);
$line1_3 = str_replace('DOK0',$dok0,$line1_3);
$line1_3 = str_replace('DUQ1',$dok11,$line1_3);
$line1_3 = str_replace('DUQ2',$dok12,$line1_3);
}else{
$line1_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line14.txt")){
$line1_4 = file_get_contents("keyboard/home/line14.txt");
if($line1_4 != null ){
$line1_4 = str_replace('DOK1',$dok1,$line1_4);
$line1_4 = str_replace('DOK2',$dok2,$line1_4);
$line1_4 = str_replace('DOK3',$dok3,$line1_4);
$line1_4 = str_replace('DOK4',$dok4,$line1_4);
$line1_4 = str_replace('DOK5',$dok5,$line1_4);
$line1_4 = str_replace('DOK6',$dok6,$line1_4);
$line1_4 = str_replace('DOK7',$dok7,$line1_4);
$line1_4 = str_replace('DOK8',$dok8,$line1_4);
$line1_4 = str_replace('DOK9',$dok9,$line1_4);
$line1_4 = str_replace('DOK0',$dok0,$line1_4);
$line1_4 = str_replace('DUQ1',$dok11,$line1_4);
$line1_4 = str_replace('DUQ2',$dok12,$line1_4);
}else{
$line1_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line21.txt")){
$line2_1 = file_get_contents("keyboard/home/line21.txt");
if($line2_1 != null ){
$line2_1 = str_replace('DOK1',$dok1,$line2_1);
$line2_1 = str_replace('DOK2',$dok2,$line2_1);
$line2_1 = str_replace('DOK3',$dok3,$line2_1);
$line2_1 = str_replace('DOK4',$dok4,$line2_1);
$line2_1 = str_replace('DOK5',$dok5,$line2_1);
$line2_1 = str_replace('DOK6',$dok6,$line2_1);
$line2_1 = str_replace('DOK7',$dok7,$line2_1);
$line2_1 = str_replace('DOK8',$dok8,$line2_1);
$line2_1 = str_replace('DOK9',$dok9,$line2_1);
$line2_1 = str_replace('DOK0',$dok0,$line2_1);
$line2_1 = str_replace('DUQ1',$dok11,$line2_1);
$line2_1 = str_replace('DUQ2',$dok12,$line2_1);
}else{
$line2_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line22.txt")){
$line2_2 = file_get_contents("keyboard/home/line22.txt");
if($line2_2 != null ){
$line2_2 = str_replace('DOK1',$dok1,$line2_2);
$line2_2 = str_replace('DOK2',$dok2,$line2_2);
$line2_2 = str_replace('DOK3',$dok3,$line2_2);
$line2_2 = str_replace('DOK4',$dok4,$line2_2);
$line2_2 = str_replace('DOK5',$dok5,$line2_2);
$line2_2 = str_replace('DOK6',$dok6,$line2_2);
$line2_2 = str_replace('DOK7',$dok7,$line2_2);
$line2_2 = str_replace('DOK8',$dok8,$line2_2);
$line2_2 = str_replace('DOK9',$dok9,$line2_2);
$line2_2 = str_replace('DOK0',$dok0,$line2_2);
$line2_2 = str_replace('DUQ1',$dok11,$line2_2);
$line2_2 = str_replace('DUQ2',$dok12,$line2_2);
}else{
$line2_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line23.txt")){
$line2_3 = file_get_contents("keyboard/home/line23.txt");
if($line2_3 != null ){
$line2_3 = str_replace('DOK1',$dok1,$line2_3);
$line2_3 = str_replace('DOK2',$dok2,$line2_3);
$line2_3 = str_replace('DOK3',$dok3,$line2_3);
$line2_3 = str_replace('DOK4',$dok4,$line2_3);
$line2_3 = str_replace('DOK5',$dok5,$line2_3);
$line2_3 = str_replace('DOK6',$dok6,$line2_3);
$line2_3 = str_replace('DOK7',$dok7,$line2_3);
$line2_3 = str_replace('DOK8',$dok8,$line2_3);
$line2_3 = str_replace('DOK9',$dok9,$line2_3);
$line2_3 = str_replace('DOK0',$dok0,$line2_3);
$line2_3 = str_replace('DUQ1',$dok11,$line2_3);
$line2_3 = str_replace('DUQ2',$dok12,$line2_3);
}else{
$line2_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line24.txt")){
$line2_4 = file_get_contents("keyboard/home/line24.txt");
if($line2_4 != null ){
$line2_4 = str_replace('DOK1',$dok1,$line2_4);
$line2_4 = str_replace('DOK2',$dok2,$line2_4);
$line2_4 = str_replace('DOK3',$dok3,$line2_4);
$line2_4 = str_replace('DOK4',$dok4,$line2_4);
$line2_4 = str_replace('DOK5',$dok5,$line2_4);
$line2_4 = str_replace('DOK6',$dok6,$line2_4);
$line2_4 = str_replace('DOK7',$dok7,$line2_4);
$line2_4 = str_replace('DOK8',$dok8,$line2_4);
$line2_4 = str_replace('DOK9',$dok9,$line2_4);
$line2_4 = str_replace('DOK0',$dok0,$line2_4);
$line2_4 = str_replace('DUQ1',$dok11,$line2_4);
$line2_4 = str_replace('DUQ2',$dok12,$line2_4);
}else{
$line2_4 = "";
}}

//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line31.txt")){
$line3_1 = file_get_contents("keyboard/home/line31.txt");
if($line3_1 != null ){
$line3_1 = str_replace('DOK1',$dok1,$line3_1);
$line3_1 = str_replace('DOK2',$dok2,$line3_1);
$line3_1 = str_replace('DOK3',$dok3,$line3_1);
$line3_1 = str_replace('DOK4',$dok4,$line3_1);
$line3_1 = str_replace('DOK5',$dok5,$line3_1);
$line3_1 = str_replace('DOK6',$dok6,$line3_1);
$line3_1 = str_replace('DOK7',$dok7,$line3_1);
$line3_1 = str_replace('DOK8',$dok8,$line3_1);
$line3_1 = str_replace('DOK9',$dok9,$line3_1);
$line3_1 = str_replace('DOK0',$dok0,$line3_1);
$line3_1 = str_replace('DUQ1',$dok11,$line3_1);
$line3_1 = str_replace('DUQ2',$dok12,$line3_1);
}else{
$line3_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line32.txt")){
$line3_2 = file_get_contents("keyboard/home/line32.txt");
if($line3_2 != null ){
$line3_2 = str_replace('DOK1',$dok1,$line3_2);
$line3_2 = str_replace('DOK2',$dok2,$line3_2);
$line3_2 = str_replace('DOK3',$dok3,$line3_2);
$line3_2 = str_replace('DOK4',$dok4,$line3_2);
$line3_2 = str_replace('DOK5',$dok5,$line3_2);
$line3_2 = str_replace('DOK6',$dok6,$line3_2);
$line3_2 = str_replace('DOK7',$dok7,$line3_2);
$line3_2 = str_replace('DOK8',$dok8,$line3_2);
$line3_2 = str_replace('DOK9',$dok9,$line3_2);
$line3_2 = str_replace('DOK0',$dok0,$line3_2);
$line3_2 = str_replace('DUQ1',$dok11,$line3_2);
$line3_2 = str_replace('DUQ2',$dok12,$line3_2);
}else{
$line3_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line33.txt")){
$line3_3 = file_get_contents("keyboard/home/line33.txt");
if($line3_3 != null ){
$line3_3 = str_replace('DOK1',$dok1,$line3_3);
$line3_3 = str_replace('DOK2',$dok2,$line3_3);
$line3_3 = str_replace('DOK3',$dok3,$line3_3);
$line3_3 = str_replace('DOK4',$dok4,$line3_3);
$line3_3 = str_replace('DOK5',$dok5,$line3_3);
$line3_3 = str_replace('DOK6',$dok6,$line3_3);
$line3_3 = str_replace('DOK7',$dok7,$line3_3);
$line3_3 = str_replace('DOK8',$dok8,$line3_3);
$line3_3 = str_replace('DOK9',$dok9,$line3_3);
$line3_3 = str_replace('DOK0',$dok0,$line3_3);
$line3_3 = str_replace('DUQ1',$dok11,$line3_3);
$line3_3 = str_replace('DUQ2',$dok12,$line3_3);
}else{
$line3_3 = "";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line34.txt")){
$line3_4 = file_get_contents("keyboard/home/line34.txt");
if($line3_4 != null ){
$line3_4 = str_replace('DOK1',$dok1,$line3_4);
$line3_4 = str_replace('DOK2',$dok2,$line3_4);
$line3_4 = str_replace('DOK3',$dok3,$line3_4);
$line3_4 = str_replace('DOK4',$dok4,$line3_4);
$line3_4 = str_replace('DOK5',$dok5,$line3_4);
$line3_4 = str_replace('DOK6',$dok6,$line3_4);
$line3_4 = str_replace('DOK7',$dok7,$line3_4);
$line3_4 = str_replace('DOK8',$dok8,$line3_4);
$line3_4 = str_replace('DOK9',$dok9,$line3_4);
$line3_4 = str_replace('DOK0',$dok0,$line3_4);
$line3_4 = str_replace('DUQ1',$dok11,$line3_4);
$line3_4 = str_replace('DUQ2',$dok12,$line3_4);
}else{
$line3_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line41.txt")){
$line4_1 = file_get_contents("keyboard/home/line41.txt");
if($line4_1 != null ){
$line4_1 = str_replace('DOK1',$dok1,$line4_1);
$line4_1 = str_replace('DOK2',$dok2,$line4_1);
$line4_1 = str_replace('DOK3',$dok3,$line4_1);
$line4_1 = str_replace('DOK4',$dok4,$line4_1);
$line4_1 = str_replace('DOK5',$dok5,$line4_1);
$line4_1 = str_replace('DOK6',$dok6,$line4_1);
$line4_1 = str_replace('DOK7',$dok7,$line4_1);
$line4_1 = str_replace('DOK8',$dok8,$line4_1);
$line4_1 = str_replace('DOK9',$dok9,$line4_1);
$line4_1 = str_replace('DOK0',$dok0,$line4_1);
$line4_1 = str_replace('DUQ1',$dok11,$line4_1);
$line4_1 = str_replace('DUQ2',$dok12,$line4_1);
}else{
$line4_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line42.txt")){
$line4_2 = file_get_contents("keyboard/home/line42.txt");
if($line4_2 != null ){
$line4_2 = str_replace('DOK1',$dok1,$line4_2);
$line4_2 = str_replace('DOK2',$dok2,$line4_2);
$line4_2 = str_replace('DOK3',$dok3,$line4_2);
$line4_2 = str_replace('DOK4',$dok4,$line4_2);
$line4_2 = str_replace('DOK5',$dok5,$line4_2);
$line4_2 = str_replace('DOK6',$dok6,$line4_2);
$line4_2 = str_replace('DOK7',$dok7,$line4_2);
$line4_2 = str_replace('DOK8',$dok8,$line4_2);
$line4_2 = str_replace('DOK9',$dok9,$line4_2);
$line4_2 = str_replace('DOK0',$dok0,$line4_2);
$line4_2 = str_replace('DUQ1',$dok11,$line4_2);
$line4_2 = str_replace('DUQ2',$dok12,$line4_2);
}else{
$line4_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line43.txt")){
$line4_3 = file_get_contents("keyboard/home/line43.txt");
if($line4_3 != null ){
$line4_3 = str_replace('DOK1',$dok1,$line4_3);
$line4_3 = str_replace('DOK2',$dok2,$line4_3);
$line4_3 = str_replace('DOK3',$dok3,$line4_3);
$line4_3 = str_replace('DOK4',$dok4,$line4_3);
$line4_3 = str_replace('DOK5',$dok5,$line4_3);
$line4_3 = str_replace('DOK6',$dok6,$line4_3);
$line4_3 = str_replace('DOK7',$dok7,$line4_3);
$line4_3 = str_replace('DOK8',$dok8,$line4_3);
$line4_3 = str_replace('DOK9',$dok9,$line4_3);
$line4_3 = str_replace('DOK0',$dok0,$line4_3);
$line4_3 = str_replace('DUQ1',$dok11,$line4_3);
$line4_3 = str_replace('DUQ2',$dok12,$line4_3);
}else{
$line4_3 = "";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line44.txt")){
$line4_4 = file_get_contents("keyboard/home/line44.txt");
if($line4_4 != null ){
$line4_4 = str_replace('DOK1',$dok1,$line4_4);
$line4_4 = str_replace('DOK2',$dok2,$line4_4);
$line4_4 = str_replace('DOK3',$dok3,$line4_4);
$line4_4 = str_replace('DOK4',$dok4,$line4_4);
$line4_4 = str_replace('DOK5',$dok5,$line4_4);
$line4_4 = str_replace('DOK6',$dok6,$line4_4);
$line4_4 = str_replace('DOK7',$dok7,$line4_4);
$line4_4 = str_replace('DOK8',$dok8,$line4_4);
$line4_4 = str_replace('DOK9',$dok9,$line4_4);
$line4_4 = str_replace('DOK0',$dok0,$line4_4);
$line4_4 = str_replace('DUQ1',$dok11,$line4_4);
$line4_4 = str_replace('DUQ2',$dok12,$line4_4);
}else{
$line4_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line51.txt")){
$line5_1 = file_get_contents("keyboard/home/line51.txt");
if($line5_1 != null ){
$line5_1 = str_replace('DOK1',$dok1,$line5_1);
$line5_1 = str_replace('DOK2',$dok2,$line5_1);
$line5_1 = str_replace('DOK3',$dok3,$line5_1);
$line5_1 = str_replace('DOK4',$dok4,$line5_1);
$line5_1 = str_replace('DOK5',$dok5,$line5_1);
$line5_1 = str_replace('DOK6',$dok6,$line5_1);
$line5_1 = str_replace('DOK7',$dok7,$line5_1);
$line5_1 = str_replace('DOK8',$dok8,$line5_1);
$line5_1 = str_replace('DOK9',$dok9,$line5_1);
$line5_1 = str_replace('DOK0',$dok0,$line5_1);
$line5_1 = str_replace('DUQ1',$dok11,$line5_1);
$line5_1 = str_replace('DUQ2',$dok12,$line5_1);
}else{
$line5_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line52.txt")){
$line5_2 = file_get_contents("keyboard/home/line52.txt");
if($line5_2 != null ){
$line5_2 = str_replace('DOK1',$dok1,$line5_2);
$line5_2 = str_replace('DOK2',$dok2,$line5_2);
$line5_2 = str_replace('DOK3',$dok3,$line5_2);
$line5_2 = str_replace('DOK4',$dok4,$line5_2);
$line5_2 = str_replace('DOK5',$dok5,$line5_2);
$line5_2 = str_replace('DOK6',$dok6,$line5_2);
$line5_2 = str_replace('DOK7',$dok7,$line5_2);
$line5_2 = str_replace('DOK8',$dok8,$line5_2);
$line5_2 = str_replace('DOK9',$dok9,$line5_2);
$line5_2 = str_replace('DOK0',$dok0,$line5_2);
$line5_2 = str_replace('DUQ1',$dok11,$line5_2);
$line5_2 = str_replace('DUQ2',$dok12,$line5_2);
}else{
$line5_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line53.txt")){
$line5_3 = file_get_contents("keyboard/home/line53.txt");
if($line5_3 != null ){
$line5_3 = str_replace('DOK1',$dok1,$line5_3);
$line5_3 = str_replace('DOK2',$dok2,$line5_3);
$line5_3 = str_replace('DOK3',$dok3,$line5_3);
$line5_3 = str_replace('DOK4',$dok4,$line5_3);
$line5_3 = str_replace('DOK5',$dok5,$line5_3);
$line5_3 = str_replace('DOK6',$dok6,$line5_3);
$line5_3 = str_replace('DOK7',$dok7,$line5_3);
$line5_3 = str_replace('DOK8',$dok8,$line5_3);
$line5_3 = str_replace('DOK9',$dok9,$line5_3);
$line5_3 = str_replace('DOK0',$dok0,$line5_3);
$line5_3 = str_replace('DUQ1',$dok11,$line5_3);
$line5_3 = str_replace('DUQ2',$dok12,$line5_3);
}else{
$line5_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line54.txt")){
$line5_4 = file_get_contents("keyboard/home/line54.txt");
if($line5_4 != null ){
$line5_4 = str_replace('DOK1',$dok1,$line5_4);
$line5_4 = str_replace('DOK2',$dok2,$line5_4);
$line5_4 = str_replace('DOK3',$dok3,$line5_4);
$line5_4 = str_replace('DOK4',$dok4,$line5_4);
$line5_4 = str_replace('DOK5',$dok5,$line5_4);
$line5_4 = str_replace('DOK6',$dok6,$line5_4);
$line5_4 = str_replace('DOK7',$dok7,$line5_4);
$line5_4 = str_replace('DOK8',$dok8,$line5_4);
$line5_4 = str_replace('DOK9',$dok9,$line5_4);
$line5_4 = str_replace('DOK0',$dok0,$line5_4);
$line5_4 = str_replace('DUQ1',$dok11,$line5_4);
$line5_4 = str_replace('DUQ2',$dok12,$line5_4);
}else{
$line5_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line61.txt")){
$line6_1 = file_get_contents("keyboard/home/line61.txt");
if($line6_1 != null ){
$line6_1 = str_replace('DOK1',$dok1,$line6_1);
$line6_1 = str_replace('DOK2',$dok2,$line6_1);
$line6_1 = str_replace('DOK3',$dok3,$line6_1);
$line6_1 = str_replace('DOK4',$dok4,$line6_1);
$line6_1 = str_replace('DOK5',$dok5,$line6_1);
$line6_1 = str_replace('DOK6',$dok6,$line6_1);
$line6_1 = str_replace('DOK7',$dok7,$line6_1);
$line6_1 = str_replace('DOK8',$dok8,$line6_1);
$line6_1 = str_replace('DOK9',$dok9,$line6_1);
$line6_1 = str_replace('DOK0',$dok0,$line6_1);
$line6_1 = str_replace('DUQ1',$dok11,$line6_1);
$line6_1 = str_replace('DUQ2',$dok12,$line6_1);
}else{
$line6_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line62.txt")){
$line6_2 = file_get_contents("keyboard/home/line62.txt");
if($line6_2 != null ){
$line6_2 = str_replace('DOK1',$dok1,$line6_2);
$line6_2 = str_replace('DOK2',$dok2,$line6_2);
$line6_2 = str_replace('DOK3',$dok3,$line6_2);
$line6_2 = str_replace('DOK4',$dok4,$line6_2);
$line6_2 = str_replace('DOK5',$dok5,$line6_2);
$line6_2 = str_replace('DOK6',$dok6,$line6_2);
$line6_2 = str_replace('DOK7',$dok7,$line6_2);
$line6_2 = str_replace('DOK8',$dok8,$line6_2);
$line6_2 = str_replace('DOK9',$dok9,$line6_2);
$line6_2 = str_replace('DOK0',$dok0,$line6_2);
$line6_2 = str_replace('DUQ1',$dok11,$line6_2);
$line6_2 = str_replace('DUQ2',$dok12,$line6_2);
}else{
$line6_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line63.txt")){
$line6_3 = file_get_contents("keyboard/home/line63.txt");
if($line6_3 != null ){
$line6_3 = str_replace('DOK1',$dok1,$line6_3);
$line6_3 = str_replace('DOK2',$dok2,$line6_3);
$line6_3 = str_replace('DOK3',$dok3,$line6_3);
$line6_3 = str_replace('DOK4',$dok4,$line6_3);
$line6_3 = str_replace('DOK5',$dok5,$line6_3);
$line6_3 = str_replace('DOK6',$dok6,$line6_3);
$line6_3 = str_replace('DOK7',$dok7,$line6_3);
$line6_3 = str_replace('DOK8',$dok8,$line6_3);
$line6_3 = str_replace('DOK9',$dok9,$line6_3);
$line6_3 = str_replace('DOK0',$dok0,$line6_3);
$line6_3 = str_replace('DUQ1',$dok11,$line6_3);
$line6_3 = str_replace('DUQ2',$dok12,$line6_3);
}else{
$line6_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line64.txt")){
$line6_4 = file_get_contents("keyboard/home/line64.txt");
if($line6_4 != null ){
$line6_4 = str_replace('DOK1',$dok1,$line6_4);
$line6_4 = str_replace('DOK2',$dok2,$line6_4);
$line6_4 = str_replace('DOK3',$dok3,$line6_4);
$line6_4 = str_replace('DOK4',$dok4,$line6_4);
$line6_4 = str_replace('DOK5',$dok5,$line6_4);
$line6_4 = str_replace('DOK6',$dok6,$line6_4);
$line6_4 = str_replace('DOK7',$dok7,$line6_4);
$line6_4 = str_replace('DOK8',$dok8,$line6_4);
$line6_4 = str_replace('DOK9',$dok9,$line6_4);
$line6_4 = str_replace('DOK0',$dok0,$line6_4);
$line6_4 = str_replace('DUQ1',$dok11,$line6_4);
$line6_4 = str_replace('DUQ2',$dok12,$line6_4);
}else{
$line6_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line71.txt")){
$line7_1 = file_get_contents("keyboard/home/line71.txt");
if($line7_1 != null ){
$line7_1 = str_replace('DOK1',$dok1,$line7_1);
$line7_1 = str_replace('DOK2',$dok2,$line7_1);
$line7_1 = str_replace('DOK3',$dok3,$line7_1);
$line7_1 = str_replace('DOK4',$dok4,$line7_1);
$line7_1 = str_replace('DOK5',$dok5,$line7_1);
$line7_1 = str_replace('DOK6',$dok6,$line7_1);
$line7_1 = str_replace('DOK7',$dok7,$line7_1);
$line7_1 = str_replace('DOK8',$dok8,$line7_1);
$line7_1 = str_replace('DOK9',$dok9,$line7_1);
$line7_1 = str_replace('DOK0',$dok0,$line7_1);
$line7_1 = str_replace('DUQ1',$dok11,$line7_1);
$line7_1 = str_replace('DUQ2',$dok12,$line7_1);
}else{
$line7_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line72.txt")){
$line7_2 = file_get_contents("keyboard/home/line72.txt");
if($line7_2 != null ){
$line7_2 = str_replace('DOK1',$dok1,$line7_2);
$line7_2 = str_replace('DOK2',$dok2,$line7_2);
$line7_2 = str_replace('DOK3',$dok3,$line7_2);
$line7_2 = str_replace('DOK4',$dok4,$line7_2);
$line7_2 = str_replace('DOK5',$dok5,$line7_2);
$line7_2 = str_replace('DOK6',$dok6,$line7_2);
$line7_2 = str_replace('DOK7',$dok7,$line7_2);
$line7_2 = str_replace('DOK8',$dok8,$line7_2);
$line7_2 = str_replace('DOK9',$dok9,$line7_2);
$line7_2 = str_replace('DOK0',$dok0,$line7_2);
$line7_2 = str_replace('DUQ1',$dok11,$line7_2);
$line7_2 = str_replace('DUQ2',$dok12,$line7_2);
}else{
$line7_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line73.txt")){
$line7_3 = file_get_contents("keyboard/home/line73.txt");
if($line7_3 != null ){
$line7_3 = str_replace('DOK1',$dok1,$line7_3);
$line7_3 = str_replace('DOK2',$dok2,$line7_3);
$line7_3 = str_replace('DOK3',$dok3,$line7_3);
$line7_3 = str_replace('DOK4',$dok4,$line7_3);
$line7_3 = str_replace('DOK5',$dok5,$line7_3);
$line7_3 = str_replace('DOK6',$dok6,$line7_3);
$line7_3 = str_replace('DOK7',$dok7,$line7_3);
$line7_3 = str_replace('DOK8',$dok8,$line7_3);
$line7_3 = str_replace('DOK9',$dok9,$line7_3);
$line7_3 = str_replace('DOK0',$dok0,$line7_3);
$line7_3 = str_replace('DUQ1',$dok11,$line7_3);
$line7_3 = str_replace('DUQ2',$dok12,$line7_3);
}else{
$line7_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line74.txt")){
$line7_4 = file_get_contents("keyboard/home/line74.txt");
if($line7_4 != null ){
$line7_4 = str_replace('DOK1',$dok1,$line7_4);
$line7_4 = str_replace('DOK2',$dok2,$line7_4);
$line7_4 = str_replace('DOK3',$dok3,$line7_4);
$line7_4 = str_replace('DOK4',$dok4,$line7_4);
$line7_4 = str_replace('DOK5',$dok5,$line7_4);
$line7_4 = str_replace('DOK6',$dok6,$line7_4);
$line7_4 = str_replace('DOK7',$dok7,$line7_4);
$line7_4 = str_replace('DOK8',$dok8,$line7_4);
$line7_4 = str_replace('DOK9',$dok9,$line7_4);
$line7_4 = str_replace('DOK0',$dok0,$line7_4);
$line7_4 = str_replace('DUQ1',$dok11,$line7_4);
$line7_4 = str_replace('DUQ2',$dok12,$line7_4);
}else{
$line7_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line81.txt")){
$line8_1 = file_get_contents("keyboard/home/line81.txt");
if($line8_1 != null ){
$line8_1 = str_replace('DOK1',$dok1,$line8_1);
$line8_1 = str_replace('DOK2',$dok2,$line8_1);
$line8_1 = str_replace('DOK3',$dok3,$line8_1);
$line8_1 = str_replace('DOK4',$dok4,$line8_1);
$line8_1 = str_replace('DOK5',$dok5,$line8_1);
$line8_1 = str_replace('DOK6',$dok6,$line8_1);
$line8_1 = str_replace('DOK7',$dok7,$line8_1);
$line8_1 = str_replace('DOK8',$dok8,$line8_1);
$line8_1 = str_replace('DOK9',$dok9,$line8_1);
$line8_1 = str_replace('DOK0',$dok0,$line8_1);
$line8_1 = str_replace('DUQ1',$dok11,$line8_1);
$line8_1 = str_replace('DUQ2',$dok12,$line8_1);
}else{
$line8_1 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line82.txt")){
$line8_2 = file_get_contents("keyboard/home/line82.txt");
if($line8_2 != null ){
$line8_2 = str_replace('DOK1',$dok1,$line8_2);
$line8_2 = str_replace('DOK2',$dok2,$line8_2);
$line8_2 = str_replace('DOK3',$dok3,$line8_2);
$line8_2 = str_replace('DOK4',$dok4,$line8_2);
$line8_2 = str_replace('DOK5',$dok5,$line8_2);
$line8_2 = str_replace('DOK6',$dok6,$line8_2);
$line8_2 = str_replace('DOK7',$dok7,$line8_2);
$line8_2 = str_replace('DOK8',$dok8,$line8_2);
$line8_2 = str_replace('DOK9',$dok9,$line8_2);
$line8_2 = str_replace('DOK0',$dok0,$line8_2);
$line8_2 = str_replace('DUQ1',$dok11,$line8_2);
$line8_2 = str_replace('DUQ2',$dok12,$line8_2);
}else{
$line8_2 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line83.txt")){
$line8_3 = file_get_contents("keyboard/home/line83.txt");
if($line8_3 != null ){
$line8_3 = str_replace('DOK1',$dok1,$line8_3);
$line8_3 = str_replace('DOK2',$dok2,$line8_3);
$line8_3 = str_replace('DOK3',$dok3,$line8_3);
$line8_3 = str_replace('DOK4',$dok4,$line8_3);
$line8_3 = str_replace('DOK5',$dok5,$line8_3);
$line8_3 = str_replace('DOK6',$dok6,$line8_3);
$line8_3 = str_replace('DOK7',$dok7,$line8_3);
$line8_3 = str_replace('DOK8',$dok8,$line8_3);
$line8_3 = str_replace('DOK9',$dok9,$line8_3);
$line8_3 = str_replace('DOK0',$dok0,$line8_3);
$line8_3 = str_replace('DUQ1',$dok11,$line8_3);
$line8_3 = str_replace('DUQ2',$dok12,$line8_3);
}else{
$line8_3 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line84.txt")){
$line8_4 = file_get_contents("keyboard/home/line84.txt");
if($line8_4 != null ){
$line8_4 = str_replace('DOK1',$dok1,$line8_4);
$line8_4 = str_replace('DOK2',$dok2,$line8_4);
$line8_4 = str_replace('DOK3',$dok3,$line8_4);
$line8_4 = str_replace('DOK4',$dok4,$line8_4);
$line8_4 = str_replace('DOK5',$dok5,$line8_4);
$line8_4 = str_replace('DOK6',$dok6,$line8_4);
$line8_4 = str_replace('DOK7',$dok7,$line8_4);
$line8_4 = str_replace('DOK8',$dok8,$line8_4);
$line8_4 = str_replace('DOK9',$dok9,$line8_4);
$line8_4 = str_replace('DOK0',$dok0,$line8_4);
$line8_4 = str_replace('DUQ1',$dok11,$line8_4);
$line8_4 = str_replace('DUQ2',$dok12,$line8_4);
}else{
$line8_4 = "";
}}
//////////------------------------\\\\\\\\\\\\\\
?>