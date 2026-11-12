# گزارش آزمایش Emoji Forge AI — اجرا شده ۲۰۲۶-۱۰-۲۸ (نسخه ۰٫۳)

دستور: ‎.venv/bin/python -m unittest discover tests‎
نتیجه نهایی: **۳۷ آزمایش، همه پاس**.
همچنین: ‎py_compile‎ موفق بود؛ ‎python3 run.py‎ بدون توکن با کد خروج ۲ متوقف شد؛
با توکن ساختگی، خطای ۴۰۱ تلگرام در ۱٫۵ ثانیه برگشت (یعنی مسیر شبکه تا
api.telegram.org از این محیط کار می‌کند، ولی توکن واقعی نیست).

## نقص‌های واقعی پیدا و رفع‌شده در نسخه ۰٫۳

1. **WEBM ایموجی دقیقاً ۱۰۰×۱۰۰ نبود:** موتور WEBM فریم‌ها را با همان ابعاد
   ورودی بازکدگذاری می‌کرد. رفع: برای خروجی ایموجی، هر فریم به مربع ۱۰۰×۱۰۰
   تبدیل می‌شود؛ برای استیکر، ضلع بزرگ ۵۱۲. تست خروجی ۱۰۰×۱۰۰ اضافه شد.
2. **TGS با رنگ متحرک (Keyframe) کرش می‌کرد:** بازرنگی، لیست Keyframe را
   رنگ ثابت فرض می‌کرد و `float(dict)` خطا می‌داد. رفع: فقط رنگ‌های ثابت
   تغییر می‌کنند؛ رنگ‌های Keyframe رد می‌شوند و در گزارش هشدار می‌آید.
3. **واترمارک روی لوگو می‌افتاد:** اگر محل واترمارک و لوگو یکی بود، هم‌پوشانی
   می‌شد. رفع: واترمارک به گوشه دیگر منتقل می‌شود و هشدار آن در گزارش می‌آید.
4. **ناحیه پاک‌سازی بزرگ بدون هشدار بود:** حالا اگر ناحیه بیش از ۲۵٪ تصویر
   باشد، هشدار تخت‌شدن بافت در گزارش می‌آید.

## نقص‌های واقعی که رفع نشده‌اند (چون به مدل خارجی نیاز دارند)

- پاک‌سازی بافت پیچیده: پرکردن با میانگین حاشیه، بافت را تخت می‌کند
  (تست شطرنجی: واریانس ناحیه پس از پاک‌سازی صفر می‌شود). این Inpainting نیست
  و کیفیت حرفه‌ای برای بافت پیچیده تضمین نمی‌شود.
- قاعده رنگ پوست: لباس نارنجی گرم (۲۳۰،۱۵۰،۸۰) هم «پوست» تشخیص داده می‌شود
  و در بازرنگی حفظ می‌شود. بدون تشخیص چهره، تفکیک کامل ممکن نیست؛
  پیش‌نمایش تنها ضامن است.
- لایه‌های تو در توی TGS (Precomp در Assets) پنهان نمی‌شوند؛ فقط لایه‌های
  سطح بالا پنهان می‌شوند. لوگوی داخل عکس/Asset در TGS هم پردازش نمی‌شود.
- لوگوی WEBM ثابت می‌ماند و شخصیت متحرک را دنبال نمی‌کند؛ فقط چون در همه
  فریم‌ها یکسان است، «لرزش» ندارد — دنبال‌کردن سوژه ادعا نمی‌شود.
- پک Adaptive (تک‌رنگِ بازرنگی‌شونده با `needs_repainting`) پشتیبانی نمی‌شود.

## زنجیره شناسایی پک — نتیجه بررسی (مهم)

دریافت یک Custom Emoji **تضمین نمی‌کند** که پک اصلی و همه اعضایش پیدا شوند.
زنجیره واقعی این است: پیام باید Entity با `custom_emoji_id` داشته باشد →
`getCustomEmojiStickers` → فیلد `set_name` در Sticker **اختیاری** است →
فقط اگر `set_name` بود، `getStickerSet` فهرست را می‌دهد → `getFile` هر فایل.
اگر `set_name` نبود، یا پیام به‌صورت استیکر (نه ایموجی متنی) آمده بود،
یا پک حذف/تغییرنام شده بود، زنجیره همان‌جا می‌شکند و فقط همان ایموجی
(یا لینک دستی پک / فایل مستقیم) باقی می‌ماند. این نتیجه از مستندات رسمی
است، نه از نام تابع؛ و رفتار زنده آن هنوز با توکن واقعی تست نشده است.

## تغییرات نسخه ۰٫۲ نسبت به ۰٫۱ (همین اجرا)

- پیش‌نمایش واقعی شد: فایل اصلی و نهایی نمونه‌ها جداگانه فرستاده می‌شوند، نه فقط گزارش متنی.
- انتشار واقعی شد: `publish.py` + کارگر انتشار (upload → create → add)؛
  تست انتشار با API شبیه‌سازی‌شده (Mock) پاس شد — نه با تلگرام واقعی.
- چهار حالت پردازش: preserve / clean / redesign / branding (نام قدیمی faithful به branding نگاشت می‌شود).
- واترمارک سفارشی با شفافیت و محل، پروفایل برند ذخیره‌شدنی (`/brand_save` / `/brand_load`)،
  لایه‌های TGS (`/tgs_layers` / `/tgs_hide`)، پیشرفت دانلود و پردازش، لغو کار در صف،
  تلاش مجدد دانلودهای ناموفق، و تأیید مالکیت پیش از دانلود پک کامل.
- نام فارسی: با نصب `arabic-reshaper` + `python-bidi` در venv، رسم فارسی
  کار می‌کند؛ تست محیط‌آگاه شد (با بسته‌ها = رسم می‌شود، بدون آن‌ها = عمداً رسم نمی‌شود).
- تابع مرده `webm_frame_count` حذف شد؛ یک باگ تست (پیکسل WEBP با خطای ۱ واحد)
  با بررسی تقریبی قرمز اصلاح شد، نه با تغییر منطق.

## دور اول: ۲۲ آزمایش، ۲ رد — علت و رفع

1. پاک‌سازی نوار قرمز یکدست با محوکردن، داخل نوار را قرمز نگه می‌داشت.
   رفع: الگوریتم به «میانگین حلقه بیرونی ناحیه» تغییر کرد و مستندات هم
   به‌روز شد. این Inpainting هوش مصنوعی نیست و همین‌طور هم مستند شده است.
2. تست ‎safe_filename‎ انتظار خطا برای ‎../../etc/passwd‎ داشت، در حالی که
   رفتار امن واقعی، خنثی‌سازی با basename است (خروجی ‎passwd‎).
   رفع: تست اصلاح شد تا رفتار واقعی را بسنجد، نه انتظار اشتباه را.

## نگاشت آزمایش‌های خواسته‌شده به آزمایش واقعی

| مورد خواسته‌شده | وضعیت | آزمایش / شاهد |
|---|---|---|
| دریافت Custom Emoji | ✅ با Mock | ‎test_custom_emoji_entities‎ + ‎test_get_custom_emoji_parsing_mocked‎ |
| شناسایی پک | ✅ منطق + ❌ زنده | پارس Entity و لینک و Manifest پاس شد؛ دریافت زنده از تلگرام بدون توکن انجام نشده |
| مدیریت پک غیرقابل‌دسترسی | ⚠️ جزئی | کد Bot در خطای getStickerSet مسیر جایگزین (فقط همان ایموجی / فایل مستقیم) را می‌فرستد؛ ولی آزمایش زنده با پک واقعی انجام نشده |
| دریافت فایل واقعی | ❌ زنده | بدون توکن، دانلود از تلگرام آزمایش نشده؛ دانلود فایل محلی و اعتبارسنجی آن پاس شد |
| تشخیص فرمت | ✅ | ‎test_sniff_and_validate‎ (PNG/WEBP/TGS/WEBM/EXE رد شد) |
| پاک‌سازی تصویر ثابت | ✅ | ‎test_clean_bottom_removes_logo_keeps_character‎ |
| تغییر رنگ | ✅ | ‎test_skin_preserved_by_recolor‎ |
| جایگزینی لوگو | ✅ | ‎test_logo_overlay_preserves_alpha‎ |
| حفظ شفافیت | ✅ | آلفای گوشه شفاف پس از لوگو، و خروجی RGBA در ‎test_square_100‎ |
| پردازش انیمیشن | ✅ | TGS: زمان‌بندی و پنهان‌کردن لایه؛ WEBM: فریم‌به‌فریم + VP9 تا ۲۵۶KB |
| کنترل حجم خروجی | ✅ | WEBM در حد ۲۵۶KB؛ TGS بالای ۶۴KB هشدار می‌دهد (نه رد پنهانی) |
| تولید ZIP | ✅ | ‎test_export_zip_sorted_and_report‎ |
| مدیریت خطای مدل هوش مصنوعی | ➖ ناموجود | هیچ مدل AI در نسخه اول نیست؛ پس خطای مدل هم موضوعیت ندارد. خطای FFmpeg با Exception شفاف برمی‌گردد |
| قطع و وصل ارتباط | ⚠️ کد، نه زنده | Polling خطا را لاگ و پس از ۲ ثانیه ادامه می‌دهد؛ آزمایش زنده قطع شبکه انجام نشده |
| بازیابی کارهای نیمه‌تمام | ✅ | ‎test_queue_interrupted_on_restart‎ — کار running به interrupted تبدیل می‌شود، نه done |
| جلوگیری از دسترسی غیرمجاز | ✅ | ‎test_admin_gate_and_persian_name_fallback‎ + رد فایل اجرایی + سانسور توکن |

## تأییدنشده (صریح، نه ادعای موفقیت)

- اتصال زنده ربات به تلگرام (نیاز به توکن از @BotFather، که شما باید بدهید).
- دریافت زنده یک Custom Emoji واقعی و پک کامل آن.
- ساخت زنده ست جدید با ‎createNewStickerSet‎ و لینک ‎t.me/addemoji/...‎ —
  کد آن نوشته و با Mock تست شده، ولی با تلگرام واقعی هنوز اجرا نشده است.
- تطبیق سه‌بعدی/پرسپکتیو لوگو: در نسخه اول وجود ندارد و ادعا نشده؛ لوگو
  ترکیب دوبعدی است.
- تشخیص خودکار لوگو/متن در تصویر رستری: وجود ندارد؛ کاربر ناحیه را
  انتخاب می‌کند (برای TGS، نام لایه‌ها نامزد می‌دهند).
- Inpainting هوش مصنوعی / حذف حرفه‌ای شیء در بافت پیچیده: وجود ندارد؛
  پاک‌سازی، پرکردن با میانگین حاشیه است و پیش‌نمایش ضامن آن است.

## کامل / ناقص / تأییدنشده — خلاصه (نسخه ۰٫۲)

- کامل (کد + تست محلی): هسته ربات، شناسایی پک (منطق)، دانلود (کد + پیشرفت + تلاش مجدد)،
  پاک‌سازی A و B، چهار حالت، پروفایل برند ذخیره‌شدنی، واترمارک، لایه TGS،
  موتور ثابت/TGS/WEBM، پیش‌نمایش واقعی (فایل)، کش افزایشی،
  ZIP + گزارش، صف و بازیابی، لغو، تأیید مالکیت، امنیت پایه، انتشار (کد + تست Mock).
- ناقص (جزئی، صادقانه): انتشار فقط در یک ست تک‌فرمت؛ پک مخلوط فرمت باید
  جداگانه منتشر شود یا دستی انجام شود. ویدیوی برندینگ، نوار نام نمی‌گیرد
  (فقط لوگو + واترمارک) تا از لرزش و پوشاندن تصویر جلوگیری شود.
- تأییدنشده: همه موارد زنده تلگرام در جدول بالا.

## نسخه ۰٫۴ — لایه مغز (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۴۴ آزمایش، همه پاس** (۷ آزمایش جدید لایه مغز).
- تست‌های جدید، همه بدون فراخوانی مدل: RuleBrain همیشه در دسترس و «مدل نیست»؛
  MuseBridge درخواست را در صف می‌نویسد و سپس فایل تصمیمِ نوشته‌شده را می‌خواند؛
  مغز خارجی بدون پیکربندی غیرفعال است؛ فراخوانی HTTP خارجی فقط با Mock تست شده
  و کلید در خروجی دیده نمی‌شود (تست زنده خارجی انجام نشده است).
- اجرای امن بدون توکن همچنان با کد ۲ متوقف می‌شود؛ هیچ اتصال زنده‌ای به مدل
  ادعا نشده است. جزئیات کامل بررسی در BRAIN.md است.

## نسخه ۰٫۷ — موتور تشخیص و حذف لوگوی قوی‌تر (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۵۶ آزمایش، همه پاس** (۴ آزمایش جدید موتور قوی).
- تشخیص لوگو، دیگر فقط نوار بالا/پایین نیست: نوارهای هر چهار لبه، حباب‌های
  یکدست مستطیلی در باندهای لبه، و ناحیه مشترک پک (باندی که در همه ایموجی‌های
  پک یکسان است، هر رنگ و هر ایموجی که باشد) — برای پک‌های استاتیک با ۳+ آیتم.
- پرکردن ناحیه: به‌جای میانگین تخت، درون‌یابی دوخطی مرزها (گرادیان پس‌زمینه
  حفظ می‌شود؛ تست: واریانس گرادیان بعد از پرکردن > ۰، در برابر میانگین تخت = ۰).
- TGS: لایه‌های متنی (ty=5) در حالت خام‌سازی خودکار پنهان می‌شوند، علاوه بر
  لایه‌های نامزد لوگو؛ دکمه «لایه‌های TGS» در منوی اصلی، لایه‌های نامزد اولین
  TGS را با دکمه پنهان‌کردن نشان می‌دهد.
- رنگ نامحدود: «رنگ دلخواه» و «طیف دلخواه» کد HEX متنی می‌پذیرند (#RRGGBB)،
  نه فقط رنگ‌های آماده؛ طیف از هر رنگ پایه ۵ درجه می‌سازد.
- محدودیت صادقانه (بدون ادعای هوش مصنوعی): لوگوی وسط بافت پیچیده بدون
  ناحیه مشترک پک، هنوز خودکار حذف نمی‌شود — با دکمه ناحیه یا پیش‌نمایش
  انتخاب می‌شود؛ پرکردن، درون‌یابی است نه Inpainting هوش مصنوعی.
- ربات زنده هنوز روی v0.7 راه‌اندازی نشده است: فرایند v0.6 در حال اجراست و
  بدون توکنِ دوباره‌فرستاده‌شده، کشتن آن ربات را آفلاین می‌کند؛ پس تا ارسال
  دوباره توکن، نسخه جدید فقط روی دیسک و تست‌شده است، نه زنده.

## نسخه ۰٫۸ — حفظ کاراکتر + بررسی شخصی متی + منوی تمیز (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۵۹ آزمایش، همه پاس** (۳ آزمایش جدید؛ ۲ آزمایش طیف قدیمی به
  بازسازی رنگی صریح منتقل شدند).
- علت خرابی تست کاربر، پیدا و اصلاح شد: حالت خام‌سازی پیش‌فرض کل تصویر را
  به دو رنگ برند تبدیل می‌کرد — پس رنگ موی کاراکتر هم عوض می‌شد.
  از این نسخه، خام‌سازی پیش‌فرض فقط لوگو را حذف می‌کند و نام/لوگوی کاربر
  را اضافه می‌کند؛ رنگ مو و رنگ‌های اصلی دست نمی‌خورند. بازسازی رنگی کامل
  فقط با دکمه صریح در تنظیمات پیشرفته روشن می‌شود.
- منوی اصلی از ۱۰ ردیف شلوغ به ۵ ردیف تمیز رسید: پیش‌نمایش، پردازش کامل،
  رنگ و برند من، ناحیه لوگو/لایه‌های TGS، خروجی/انتشار، تنظیمات پیشرفته.
  ارجاع‌های متنی به کامندها از پیام‌های ربات حذف شد.
- بررسی توسط متی (پیش‌فرض روشن): «پردازش کامل» دیگر موتور خودکار را فوراً
  اجرا نمی‌کند؛ درخواست بررسی در صف مغز نوشته می‌شود، متی اصل فایل‌ها را
  می‌بیند، لوگو و رنگ‌های قابل‌حفظ را مشخص می‌کند، طرح را در mati_plan.json
  می‌نویسد و ناظر داخل ربات (هر ۳۰ ثانیه) همان طرح را اجرا و تحویل می‌دهد.
  متی مستقیم در تلگرام پیام نمی‌فرستد.
- محدودیت صادقانه: تشخیص خودکار هنوز هوش مصنوعی بینایی نیست؛ برای همین
  بررسی شخصی، جایگزین اتوماسیون کامل شده است — نتیجه ممکن است چند دقیقه
  دیرتر برسد، ولی موی کاراکتر اشتباه رنگ نمی‌شود.

## نسخه ۰٫۹ — انتشار خودکار پک (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۶۱ آزمایش، همه پاس** (۲ آزمایش جدید انتشار).
- createNewStickerSet حالا sticker_format را هم می‌فرستد (animated برای TGS)؛
  قبلاً این فیلد ارسال نمی‌شد و انتشار ممکن بود با خطای API رد شود.
- ناظر ربات علاوه بر mati_plan، فایل publish_request.json را هم هر ۳۰ ثانیه
  می‌بیند و خودش کار انتشار را در صف می‌گذارد؛ کاربر دیگر نیازی به فایل ندارد.
- نام ست برای پروژه فعلی: forge_20261028_155740_3f1382_by_tzemoji_ai
  (لینک نهایی فقط وقتی در jobs.json وضعیت done با «published» آمد، معتبر است).

## نسخه ۱٫۰ — ویرایش لایه‌به‌لایه TGS + فونت خودکار و متنوع (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۶۳ آزمایش، همه پاس** (۲ آزمایش جدید ویرایش TGS و فونت).
- تصحیح ادعای قبلی: نام برند را می‌توان داخل TGS گذاشت — نه به‌صورت رستر،
  بلکه به‌صورت لایه شکل برداری: حروف فونت TrueType با fontTools به مسیرهای
  Lottie (کوادراتیک به کیوبیک) تبدیل می‌شوند و به‌عنوان لایه «Abolfazl_rwm Brand»
  بالای انیمیشن می‌نشینند. نمونه فایل ۰۰۱: +۶۵۸ بایت، هنوز زیر ۶۴KB.
- حذف واقعی: tgs_remove_layers لایه‌های لوگو را از JSON حذف می‌کند، نه فقط
  پنهان؛ لایه‌های پدر لایه‌های دیگر فقط پنهان می‌شوند تا انیمیشن نشکند.
- فونت خودکار و متنوع: ۶ سبک (پررنگ، مدرن، تکنولوژیک/مono، شیک/سریف، ساده،
  رسمی) بر اساس نام لایه‌های هر فایل انتخاب می‌شوند و در غیر این صورت با
  شاخص فایل می‌چرخند. روی پک فعلی: display ۵، mono ۳، serif ۴، bold_sans ۴،
  noto_serif ۳، liberation_sans ۱ — یعنی همه ایموجی‌ها یک فونت ندارند.
- انتشار: بین آپلودها ۳ ثانیه فاصله و در صورت 429، صبر تا retry_after + ۵
  ثانیه و تلاش مجدد — چون آپلود سریع قبلی خودش باعث 429 شده بود.
- علت خطای انتشار قبلی، جزئی: فایل‌های ۴ و ۵ (نه همه فایل‌ها) با «wrong file
  type» رد شدند؛ فایل‌های ۱ تا ۳ با همان روش قبول شدند. علت قطعی آن دو فایل
  هنوز با تست ایزوله مشخص نشده و انتشار مجدد با فایل‌های جدید (حذف لایه +
  متن) باید آن را روشن کند؛ تا آن زمان، انتشار موفق ادعا نمی‌شود.

## نسخه ۲٫۰ — حذف واترمارک هندسی + تم قرمز/سفید/مشکی + متن جای لوگو (۲۰۲۶-۱۰-۲۸)

- تست‌ها: **۶۵ آزمایش، همه پاس**.
- علت باقی‌ماندن لوگو در پک اول: واترمارک مشترک پک زیر نام‌های صحنه
  (Front 2، head، TON، FREE، Shape Layer) پنهان بود، نه زیر نام Logo/CDC؛
  حذف هندسی (tgs_remove_watermark_shapes) آن گروه را در ۱۹ فایل حذف کرد.
- علت باقی‌ماندن آبی: گرادینت‌های Stroke (gs) و گرادینت‌های تختِ داخل
  دیکشنری (a=0) اصلاً بازرنگی نمی‌شدند؛ حالا gs هم مثل gf و هر دو شکل
  k بازرنگی می‌شوند. بازبینی نسخه ۲: رنگ آبی گرادینت در هر ۲۰ فایل = ۰.
- تم جدید: خنثی‌سازی به سیاه/قرمز/سفید (تیره→مشکی، میانی→قرمز،
  روشن→سفید)، نه فقط درجه‌های قرمز.
- متن جای لوگو: قبل از حذف، تبدیل لوگوی قبلی ذخیره می‌شود و متن جدید
  دقیقاً با همان p/s/r/o (حتی کی‌فریم) و همان پدر، در جای لوگو می‌نشیند؛
  اگر پدر لوگو هم حذف می‌شد، لایه شکل (نه پیش‌کمپ) انتخاب می‌شود تا متن
  بیرون صفحه نرود (باگ فایل ۰۰۹). اندازه متن با عرض لوگوی قبلی تنظیم
  می‌شود (۲۰ تا ۸۰).

## نسخه ۴٫۰ — بازرنگی انتخابی تم + باکس روی‌صفحه لوگو + اعتبارسنجی انتشار (۲۰۲۶-۱۰-۲۹)

- تست‌ها: **۷۱ آزمایش، همه پاس** (۱ پرش: وابسته به ffmpeg/وب‌ام).
- تغییر اصلی ۱ — بازرنگی انتخابی (`src/emoji_forge/v4.py`):
  آبیِ غالب در لایه‌های تم به قرمز می‌رود (hue به ۰، lightness/saturation
  حفظ می‌شود)؛ لایه‌های سوژه (fire/flame، head/hair/eye/mouth/nose،
  chick/beak، energy/ding/glow، اعضای بدن) و رنگ‌های گرم/زرد طبیعی
  دست نمی‌خورند. برخلاف کد قدیم، گرادینت‌های کی‌فریمی و fillهای تختِ
  کی‌فریمی هم بازرنگی می‌شوند (قبلاً رد می‌شدند).
- تغییر اصلی ۲ — جایگزینی دقیق لوگو: باکس روی‌صفحه لوگوی قبلی از ترکیب
  کامل زنجیره تبدیل (لایه + والدها + پیش‌کمپ، با نمونه‌برداری کی‌فریم)
  محاسبه می‌شود و متن Abolfazl_rwm با اندازه‌ای که آن باکس را در عرض و ارتفاع
  پر کند (۲۴ تا ۲۲۰) در همان‌جا می‌نشیند؛ فقط در سطح اصلی، بدون لایه
  داخل asset (مشکوکِ شکست v3). سه باگ رندر که با گرید دیده شد و رفع شد:
  (الف) لایه متن آخرِ لیست بود و در Lottie زیر همه شکل‌ها می‌رفت —
  حالا اولِ لیست (بالاترین) درج می‌شود؛ (ب) opacity والد Null با o=0
  به فرزند ارث می‌رسید و متن را نامرئی می‌کرد — حالا فقط opacity خود
  لایه حساب می‌شود؛ (ج) چرخش ۱۸۰ درجه لوگو متن را وارونه می‌کرد
  (فایل ۰۰۲ «Z10E3») — چرخش به بازه ‎[-90,90]‎ نرمال می‌شود.
- تغییر اصلی ۳ — حذف کامل لوگو: نام‌های لوگو + برندمارک (TON/FREE/CDC/
  SVN) + شکل‌های مشترک پک (تشخیص عمومی، نه نام) + تبدیل لایه‌های لوگوی
  والد به Null کنترل (به‌جای پنهان hd=1 که علت شکست v1 فایل ۰۰۹ بود).
- تغییر اصلی ۴ — اعتبارسنجی انتشار (`src/emoji_forge/validate_tgs.py`):
  هر فایل را پیش از آپلود بررسی می‌کند و دقیقاً شماره فایل و علت را
  می‌گوید (لایه خالی ty=4، لایه پنهان، والد معلق، حجم >64KB، ابعاد،
  ساختار). مدت >۳ ثانیه فقط هشدار است چون پک اصلی خودش فایل ۴٫۰۲
  ثانیه‌ای دارد و تلگرام آن را پذیرفته بود. شغل انتشار در bot.py هم
  فایل خراب را با نام گزارش می‌کند و فاصله ۳ ثانیه + retry_after
  حفظ شده است.
- ابزار QA (`scripts/qa_v4.py`): رندر اصل در برابر v4 در ۰/۲۵/۵۰/۷۵/۱۰۰٪
  انیمیشن، گریدهای تماس در ‎/tmp/v4_qa/‎. نتیجه روی پک واقعی (۲۰/۲۰
  پردازش، اعتبارسنجی ۰ فایل مسدود): شکل مشترک پک باقی‌مانده = ۰ در هر
  ۲۰ فایل؛ لایه متن در هر ۲۰ فایل هست؛ سهم پیکسل آبی از ‎~۰٫۰۵–۰٫۴۸‎
  به ‎~۰٫۰۰۰‎ در ۱۸ فایل رسید؛ دو استثنا: فایل ۰۰۶ (شعله آبی = سوژه،
  عمداً حفظ شد) و فایل ۰۰۱ (مثلث خاکستری داخل سپر emblem هنوز دور
  متن دیده می‌شود — متن روی سپر است ولی مثلثِ داخل همان لایه حذف
  نشده؛ این تنها لوگوی باقی‌ماندهٔ قابل‌مشاهده در گرید است).
- محدودیت‌های اثبات‌نشده (ادعا نمی‌شود): رندر سمت تلگرام، انتشار زنده،
  و اینکه آیا متن داخل asset واقعاً توسط تلگرام رد می‌شود یا نه (فقط
  مشکوک از v3؛ نسخه ۴ اصلاً از asset-scope استفاده نمی‌کند). فایل ۰۱۱
  متن کوچک (اندازه ۲۴) در جای نشان عمودی TON دارد — جای دقیق ولی کوچک.
- بات زنده دست نخورده است (بدون ری‌استارت، بدون تماس با تلگرام، بدون
  توکن)؛ همه تغییرهای bot.py فقط روی دیسک‌اند و تا ری‌استارت بعدی
  توسط والد فعال نمی‌شوند.

## نسخهٔ v5 — جایگذاری در جای لوگو: شاخص، والد، ks و انیمیشنِ عین لوگوی قبلی (۲۰۲۶-۱۰-۲۹)

- انگیزه: بازخورد کاربر روی v4 (۲۰۲۶-۱۰-۲۹ ۰۸:۱۹): «لوگوی قبلی متحرک بود و به
  لایه‌های قبلی چسبیده بود؛ متن باید دقیقاً همان شکل و حالت و انیمیشن لوگوی
  قبلی را داشته باشد؛ لایه‌هایی که باید روی لوگو باشند زیر آن آمده‌اند.»
  تشخیص: v4 متن را در شاخص ۰ (بالاترین لایه) با تبدیلِ ترکیب‌شدهٔ سطح بالا
  می‌گذاشت؛ هم چیدمان (stacking) را می‌شکست و هم رابطهٔ والد/انیمیشن را از دست
  می‌داد.
- پیاده‌سازی (`src/emoji_forge/v5.py`، حالت `"v5"` در `pipeline.py`):
  لایهٔ متن برداری Abolfazl_rwm دقیقاً در شاخصِ لایهٔ لوگوی قبلی، در همان scope
  (سطح بالا یا همان asset — شامل comp_2 برای بنرهای ۱۷–۱۹)، با همان والد،
  با کپیِ عین ks (موقعیت، anchor، مقیاس، چرخش — شامل ۱۸۰ درجه، شفافیت، همهٔ
  کی‌فریم‌ها) و همان ip/op جایگزین می‌شود. اگر خود لوگو والدِ لایه‌های دیگر
  بود، آن فرزندان به لایهٔ متن reparent می‌شوند؛ اگر والدِ لوگو حذف شده بود،
  لایه‌های بازمانده به والدِ بزرگ (grandparent) برمی‌گردند، نه به سطح بالا.
  لایه‌های ty=0 که assetِ اصلی را ارجاع می‌دهند (حامل‌های متن، مثل LOGO PLACE
  در فایل ۰۰۹) حذف نمی‌شوند، چون متن جایگزین را به همهٔ نمونه‌های لوگو
  می‌رسانند. اندازهٔ متن از باکسِ روی صفحهٔ لوگو در فریم‌های قابل‌مشاهده
  (نه میانهٔ کور شامل فریم‌های مقیاس صفر، نه فریمِ pop مقیاس ۱۱۸۰٪) با زنجیرهٔ
  کامل مقیاس والد/پری‌کامپ حل می‌شود؛ بدون clamp قدیمی ۵۴–۱۱۰ (دقیق بودن مهم‌تر
  است). برای لوگوهای متنی (SVN/اینترنت) لبهٔ چپ متن با لبهٔ چپ لوگوی قبلی
  تراز می‌شود، نه مرکز — چون مرکزِ متن بنر خارج از قاب است و تراز مرکزی
  «Abolfazl_rwm» را کاملاً خارج از قاب می‌انداخت (باگِ دیده‌شده در رندر v5 اولیهٔ
  فایل ۰۱۷). بقیهٔ v4 حفظ شده: بازرنگی انتخابی تم (آبی→قرمز، سوژه‌ها حفظ)،
  حذف شکل مشترک پک، هرس لایه‌های خالی ty=4، اعتبارسنجی.
- باگ‌های زنجیرهٔ ماتریس که در مسیر v5 پیدا و اصلاح شد (در `v4.py` و `v5.py`):
  (۱) نمونه‌های ty=0 هیچ شکلی ندارند، پس گیتِ ref_box در `_on_screen_box`
  همهٔ باکس‌های asset را صفر می‌کرد؛ (۲) `_full_matrix` فقط یک سطح پری‌کامپ
  بالا می‌رفت و زنجیرهٔ سطح بالا (فایل ۰۱۷: comp_2 ← comp_0 ← Pre-comp 1)
  را از دست می‌داد؛ حالا `_chain_to_frame` بازگشتی همهٔ سطوح را تا قاب دنبال
  می‌کند. این دو باگ اندازه‌های قبلی را هم خراب کرده بودند (مثلاً فایل ۰۱۲ از
  ۳۸۵ به ۱۲۷ و بنرها از ۱۲۱ به ۷۲ رسیدند — اندازه‌های قبلی بر اساس باکسِ
  محلی بودند، نه باکسِ روی صفحه).
- ترتیب حذف اصلاح شد: حذف واترمارکِ گروهی بعد از جایگذاری انجام می‌شود، چون
  حذفِ زودهنگام لایه‌های CDC را خالی می‌کرد و هرس آن‌ها را پاک می‌کرد و چیزی
  برای جایگذاری نمی‌ماند (فایل‌های ۴، ۵، ۸، ۱۰، ۱۲، ۱۴، ۲۰ در اجرای اول v5).
- راستی‌آزمایی روی پک واقعی (۲۰/۲۰، `data/projects/forge-v5-test/final/`):
  اعتبارسنجی ۰ فایل مسدود (فقط هشدار مدت فایل ۰۰۵، مثل v4)؛ شکل مشترک پک
  باقی‌مانده = ۰ در هر ۲۰ فایل؛ لایه/گروه متن در هر ۲۰ فایل هست؛ سهم پیکسل آبی
  ~۰٫۰۰۰ در ۱۸ فایل (استثنا فایل ۰۰۶ شعلهٔ آبی = سوژه، عمداً حفظ شد).
  رندرهای اصل در برابر v5 در ۰/۲۵/۵۰/۷۵/۱۰۰٪ در ‎/tmp/v5_qa/‎
  (`file_NN_orig_vs_v5.png` + `v5_midframe_grid.png`) با چشم بررسی شد:
  فایل ۰۰۱ مثلث خاکستری v4 دیگر نیست (لایهٔ emblem جایگزین شد)؛ فایل ۰۰۹
  جلو Abolfazl_rwm در جای مثلث بزرگ و پشت گوشی Abolfazl_rwm کوچک در جای لوگوی کوچک؛
  فایل‌های ۱۷–۱۹ «E3» دقیقاً در جای «SV» قبلی؛ فایل ۰۰۴ شکل مورب روی متن
  مانده (چیدمانِ درست، برخلاف v4)؛ فایل‌های ۱۷–۱۹ نشانگر (cursor) روی متن
  مانده است. نمونه‌های ks عیناً برابر تأیید شد (فایل‌های ۱۳، ۲۰، ۹، ۱:
  ks و ip/op و والد یکسان).
- تست‌ها: ۷۵/۷۵ قبول (۱ skip مربوط به webm وابسته به ffmpeg)؛ ۴ تست جدید v5:
  حفظ شاخص/والد/ks (شامل چرخش ۴۵ درجه و کی‌فریم موقعیت)، حفظ والد، جایگذاری
  در scope asset، و اندازه با زنجیرهٔ مقیاس (مقیاس ۵۰٪ والد ≈ ۲ برابر اندازهٔ
  نقطه‌ای می‌خواهد).
- موارد صادقانهٔ باقی‌مانده: فایل ۰۱۱ متن دقیق ولی کوچک (اندازه ۱۶) در جای
  نشان عمودی TON — طبق مشخصات عمداً بزرگ نشده است. فایل ۰۱۵ متن دقیق ولی
  کوچک (اندازه ۱۱) در جای $SVN. فایل‌های ۱۷–۱۹ فقط بخش چپ «Abolfazl_rwm» (حدود
  «E3») در قاب دیده می‌شود، دقیقاً مثل «SV» قبلی که فقط بخش چپش دیده می‌شد —
  این وفاداری به جای لوگوی قبلی است، نه باگ؛ اگر کاربر نام کامل را خوانا
  بخواهد، باید جای/اندازهٔ بنر را عمداً تغییر داد (خارج از مشخصات v5).
  رندر سمت تلگرام و انتشار زنده ادعا نمی‌شود؛ انتشار کارِ والد است.
- پشتیبان: ‎~/workspace/Emoji-Forge-AI-v24-2026-10-29.tar.gz‎ (۱۱۷ KB، تمیز:
  بدون .venv/data/__pycache__، فقط ‎.env.example‎ الگو).

## v6 — Full Optimisation (2026-10-29)

| # | v5 size (B) | v6 size (B) | Δ | brand clamped | baked | kf removed |
|---|---|---|---|---|---|---|
| 1 | 12801 | 12909 | +108 | 0 | 1 | 0 |
| 2 | 9385 | 9297 | -88 | 0 | 0 | 10 |
| 3 | 8841 | 8841 | +0 | 0 | 0 | 0 |
| 4 | 4152 | 4220 | +68 | 1 | 1 | 0 |
| 5 | 40495 | 40536 | +41 | 1 | 1 | 2 |
| 6 | 29356 | 29356 | +0 | 0 | 0 | 0 |
| 7 | 10473 | 10511 | +38 | 1 | 1 | 0 |
| 8 | 19005 | 19060 | +55 | 1 | 1 | 0 |
| 9 | 8769 | 8800 | +31 | 1 | 1 | 0 |
| 10 | 2234 | 2280 | +46 | 1 | 1 | 0 |
| 11 | 42050 | 42034 | -16 | 1 | 1 | 23 |
| 12 | 4205 | 4262 | +57 | 1 | 1 | 2 |
| 13 | 11015 | 11090 | +75 | 0 | 1 | 0 |
| 14 | 37420 | 37490 | +70 | 1 | 1 | 2 |
| 15 | 3112 | 3129 | +17 | 1 | 1 | 3 |
| 16 | 8500 | 8564 | +64 | 1 | 1 | 0 |
| 17 | 2345 | 2406 | +61 | 1 | 1 | 0 |
| 18 | 2345 | 2405 | +60 | 1 | 1 | 0 |
| 19 | 2346 | 2407 | +61 | 1 | 1 | 0 |
| 20 | 18938 | 18897 | -41 | 0 | 1 | 11 |
| **total** | **277787** | **278494** | **+707** | | | |

### File 016 root cause
Stripped of gradient colours and the comp_0 text layer, v5 file 016 is
byte-identical to v3 file 016, which Telegram accepted. Every single
remaining difference also appears individually in files Telegram
accepted, so no single element is proven. The strongest hypothesis:
file 016's brand text combined THREE abnormal traits at once — op
9999 (doc op 180), ks without explicit o/r, and a group transform
offset — while v3's accepted text had op 180, explicit o=100/r=0 and
p=[0,0]. v6 normalises every brand text layer to the accepted shape
(op clamped to doc op, explicit o/r, offset baked into path
coordinates). Local proof: the improved validator flags the
reconstructed v5 file 016 with exactly those 3 issues, v6 file 016
passes, and v6 file 016 renders pixel-identical to v5 at 25/50/75%.
This is a risk-reduction fix, not a Telegram-proven root cause: no
Telegram API was used in v6, and live publish is the parent's job.

### Size
Total 277,787 -> 278,494 B (+707 B, +0.25%): HONEST RESULT — v5 files
were already near-minimal (0 unreferenced assets found; referenced
empty assets must stay, v3/v4 proved Telegram tolerates them), and
the added explicit o/r fields cost more gzip bytes than the 53
deduplicated keyframes saved. All 20 files remain < 64 KB (max
42,050 B). v6's value is normalisation and robustness, not size.

### Animation
File 009's 1180% scale pop exists identically in the original —
design, kept. All op > doc op layers remaining in v6 finals are
original layers (Null anchors etc.); every brand text layer is now
clamped to doc op. File 005's 4.02 s duration remains a warning only
(the original pack itself has it).

### Readability options (proposed, NOT applied)
- File 011: exact text (size 16) at the TON badge place vs a 2x
  enlargement — render /tmp/v6_qa/readability_11.png. 2x is much more
  readable but overlaps the boy's legs; user chooses per file.
- File 015: text is not visible at ANY sampled frame in the original
  either (disc animation), so enlargement alone cannot fix it; it
  would need deliberate repositioning, which breaks exactness. Left
  exact; reported, not silently changed.

### Bot robustness
- publish.validate_before_upload(): validator gate callable before
  any upload; bot's publish worker already aborts with the exact
  blocked file/reason (v4 gate, live since the 08:19 restart).
- publish.single_file_upload_plan(): one fixed file joins an existing
  set via validate -> uploadStickerFile -> addStickerToSet, without
  recreating the set or re-uploading the other 19 files.

### Verification
- Validator: 0 blocked files in v6 finals; v5 file 016 reconstruction
  flagged with 3 issues (regression test).
- QA (scripts/qa_v5.py against v6 dirs): pack-common shapes 0 in all
  20, Abolfazl_rwm present in all 20, blue pixels ~0.000 in 18 files (file
  006 flame blue preserved by design). Renders: /tmp/v6_qa/
  file_NN_orig_vs_v5.png, midframe grid /tmp/v6_qa/v6_midframe_grid.png
  (copy ~/workspace/v6_midframe_grid.png).
- Tests: 79/79 pass, 1 skip (ffmpeg webm). +4 v6 tests: file 016
  regression, normalisation render-identity, keyframe dedup
  conservatism, single-file plan.
- Backup: ~/workspace/Emoji-Forge-AI-v25-2026-10-29.tar.gz.
- NOT verified: Telegram-side rendering, live publish.

## v7 — Accuracy audit & repair + Abolfazl logo (2026-10-29)

User verdict on v6 (09:08): some layers that should NOT have been
deleted were deleted (file 011's spinning coin named explicitly),
classification "not precise enough", animations still wrong. Same
message supplied a new logo (`logo.png`, Abolfazl wordmark + Abolfazl_rwm) with
a new controlling rule that OVERRIDES the v5 exact-size rule for the
brand element: the logo is always BIG and readable — target on-screen
width >= 70% of canvas (358px) unless the old logo was already wider.

### Logo vectorisation (scripts/trace_logo.py)
- Cropped to the alpha bbox (2917x1269), traced the alpha mask with
  VTracer 0.6.15 (binary/spline) into SVG, converted to Lottie bezier
  subpaths in `src/emoji_forge/abolfazl_logo_shapes.json` (512-wide local
  space). Two converter bugs found and fixed by rendering, not by
  assumption: VTracer's per-path `translate()` transforms were being
  dropped, and hole winding must be normalised by signed area
  (VTracer already emits holes reversed; a naive depth-parity rule
  undid them).
- Result: 12 subpaths, 696 points, 2 holes (A counter, "0" counter).
- Fidelity (rendered vector vs original PNG alpha): IoU 0.983 at
  512px wide, IoU 0.959 at emoji size (100px). Evidence:
  /tmp/logo_work/logo_compare.png.
- In TGS the logo group is white fill + thin dark stroke (w = 4.45k
  in shape space), the same readability device as the v5/v6 text.

### Per-layer audit (scripts/audit_v7.py -> forge-v7-test/audit.md)
Every layer and shape group of the 20 originals, classified with the
rule set in `src/emoji_forge/v7.py` (pack-common geometry signatures
and brand-geometry tests first; solo-render-verified overrides for
composite elements; names last). Solo renders in /tmp/v7_qa/solo/,
original-vs-v6 difference frames in /tmp/v7_qa/diff/.

- Elements audited: 523 layers + 1256 groups = 1779
  (LOGO/BRAND 148, SCENE 1428, THEME 203).
- Falsely deleted in v6 — 20 elements, all now restored/repaired:
  - file 011: TON layer groups Group5 + GroupFon (coin disc) and
    Group 3 (coin shadow) — only Group 4 (TON glyphs) was logo;
    layers TON-Rebro-LEFT and TON-Rebro-LEFT 3 (coin rim during the
    spin; solo + in-context frames prove they are the coin's edge).
  - file 013: FREE layer Group 4 (theme square body) and Group 2
    (white frame) incl. nested Group 3 — only Group 5 was logo.
  - files 017/018/019: comp_2 layers "Logo CIrcle" and
    "Logo CIrcle 2" (the badge disc under the logo — context solos
    show a plain white disc and its blue gradient twin) plus their
    Rectangle 1 groups (2 elements per file counted at group level).
- Logos missed in v6: 0. Proven two ways: the audit's geometry-based
  missed check (a LOGO element counts as missed only if its exact
  path geometry is still present in the v6 final) and an independent
  scan of 121 brand path geometries from the originals against all
  v6 finals (0 remaining). Brand-adjacent survivors are scene parts,
  not pack marks: file 004's orbiting play triangle, file 009's phone
  app icons, file 020's pen "ASTRONAUT" lettering.
- Three no-shape layers in file 011 (Shape Layer 1, Слой-фигура 1,
  north Outlines) were also deleted by v6; they carry no geometry
  and change no pixel, and re-adding them would trip the validator's
  empty-layer rule, so they stay deleted deliberately.

### Repairs in the v7 build (src/emoji_forge/v7.py, from ORIGINALS)
- Composite main layers rebuilt (011 TON, 013 FREE): the original
  layer, its ks and its children are kept; only its logo groups are
  replaced by the brand group at the same group index.
- Falsely deleted layers restored at their original positions
  (011 rims; 017-019 discs + the "Logo in" carrier instances) and
  recoloured with the rest of the pack.
- Secondary brand slots replaced in place instead of left blank:
  file 015 comp_2 CDC, file 016 comp_1 CDC (the alternating triangle
  phase — in v6 those phases render empty), files 017-019 comp_3
  CDC (inside the badge disc). Secondary slots keep the old logo's
  exact on-screen size; the big rule applies to the main element.
- Everything else follows v5 (verbatim ks / index / scope / parent)
  + v6 normalisation, extended to the "Abolfazl Logo" name; the
  validator's brand-layer rules now also match "Abolfazl".

### Logo size per file (on-screen width at the reference frame)
Target: max(358px, old logo width). Centre: old logo's centre,
shifted only as far as needed to keep the logo on canvas (shift
column). Files 017-019 keep the old 877px banner width; their centres
sit off-canvas in the original (panning banner phases), so the shift
is large and the visible crop differs per phase — exactly as the old
text behaved, at the user's requested size.

### Animation fidelity, numeric (scripts/fidelity_v7.py)
Old logo vs brand element, every 5th frame, full transform chain.
centre/traj = max centre deviation / max trajectory deviation after
removing the median offset (px). v6 = text, v7 = Abolfazl logo.

| file | old W | logo W | shift | v6 centre/traj | v7 centre/traj | v7 rot | v7 opa |
|---|---|---|---|---|---|---|---|
| 01 | 276 | 358 | 0 | 0.0 / 0.0 | 0.13 / 0.0 | 0.0 | 0.0 |
| 02 | 237 | 358 | 0 | 0.0 / 0.0 | 0.15 / 0.03 | 0.0 | 0.0 |
| 03 | 233 | 358 | 0 | 0.01 / 0.0 | 0.13 / 0.0 | 0.0 | 0.0 |
| 04 | 277 | 358 | 0 | 0.01 / 0.0 | 0.13 / 0.02 | 0.0 | 0.0 |
| 05 | 328 | 358 | 0 | 0.0 / 0.0 | 0.1 / 0.0 | 0.0 | 0.0 |
| 06 | 193 | 358 | 0 | 0.0 / 0.0 | 0.14 / 0.06 | 0.0 | 0.0 |
| 07 | 243 | 358 | 0 | 0.0 / 0.0 | 0.15 / 0.05 | 0.0 | 0.0 |
| 08 | 283 | 358 | 0 | 0.01 / 0.0 | 0.14 / 0.01 | 0.0 | 0.0 |
| 09 | 108 | 358 | 0 | 0.02 / 0.02 | 1.44 / 1.32 | 0.0 | 0.0 |
| 10 | 204 | 358 | 0 | 0.0 / 0.0 | 0.14 / 0.12 | 0.0 | 0.0 |
| 11 | 137 | 358 | 69 | 10.71 / 2.39 | 68.82 / 45.88 | 0.0 | 0.0 |
| 12 | 114 | 358 | 0 | 0.0 / 0.0 | 0.13 / 0.0 | 0.0 | 0.0 |
| 13 | 320 | 357 | 0 | 9.36 / 1.25 | 0.16 / 0.02 | 0.0 | 0.0 |
| 14 | 202 | 358 | 0 | 0.01 / 0.0 | 0.16 / 0.11 | 0.0 | 0.0 |
| 15 | 166 | 358 | 0 | 4.37 / 1.43 | 0.14 / 0.05 | 0.0 | 0.0 |
| 16 | 242 | 358 | 0 | 2.83 / 2.32 | 0.14 / 0.1 | 0.0 | 0.0 |
| 17 | 877 | 877 | 389 | 207.29 / 0.0 | 389.08 / 0.0 | 0.0 | 0.0 |
| 18 | 877 | 877 | 0 | 207.29 / 0.0 | 0.33 / 0.0 | 0.0 | 0.0 |
| 19 | 877 | 877 | 268 | 207.29 / 0.0 | 268.02 / 0.0 | 0.0 | 0.0 |
| 20 | 102 | 358 | 36 | 0.0 / 0.0 | 40.47 / 31.08 | 0.0 | 0.0 |

Structural causes, found and addressed:
- File 016 slot 2: v6 has NO element in comp_1 (centre deviation =
  the whole slot, phases blank). v7 replaces it in place: centre dev
  0.09px, traj dev 0.04px over 25 compared frames. (Its scale dev is
  52% — the logo's aspect 0.435 vs the triangle's 0.9; width matches,
  height cannot, for a fixed artwork.)
- Files 017/019 centre deviation = the documented clamp shift
  (constant; traj 0.0 — the movement itself is exact). v6's 207.29px
  on all three banners was v5's left-edge text alignment offset.
- Files 011/020 trajectory deviation (45.88 / 31.08px): NOT a glue
  error — the logo keeps the slot's verbatim ks and parent. The
  centre clamp is a screen-space shift; inside a slot whose scale-x
  flips (011 coin) or which rotates (020 ball), that shift is a
  local-space offset that swings with the slot's own animation, so
  the logo centre orbits the old glyph centre. Any placement that is
  both big and on-canvas at the rest frame has this property in
  these two slots; removing it would require breaking the glue
  (counter-animation), which the brief forbids.
- Everywhere else v7 centre/traj deviation <= 1.44px, rotation and
  opacity deviation 0.0 — the logo follows the old logo's animation
  frame-for-frame through the full parent/precomp chain.

### Validation / tests / QA
- Validator (improved, Abolfazl-aware): 0 blocked on all 20 finals;
  1 warning total (file 005 duration 4.02s — the original pack's own
  file, warning by design).
- Sizes: 11,613–50,920 bytes (all < 64KB; the traced logo adds
  ~5–19KB per file vs v6).
- Tests: 85 passed, 1 skipped (ffmpeg WEBM) — includes 6 new v7
  tests (trace asset shape/holes, classification of composite and
  restored elements, coin restoration + logo width on file 011,
  square/frame kept on 013, disc + secondary on 017, alternating
  slot on 016).
- QA (scripts/qa_v7.py): pack-common signatures remaining 0/20;
  Abolfazl Logo present 20/20; blue fraction -> ~0.000 in 18 files
  (file 006 flame stays blue by design — subject preservation;
  file 011 0.008 from the boy's shirt). Per-file orig-vs-v7 strips
  and the midframe grid in /tmp/v7_qa/ (grid also at
  ~/workspace/v7_midframe_grid.png).
- File 011 coin proof: /tmp/v7_qa/file_11_coin_proof.png (final,
  frames 0/15/22/30/38/45/90) and /tmp/v7_qa/file_11_coin_solo.png
  (same frames with the logo group removed): the red coin disc is
  full at 0/45/90, narrow at 15/22, edge-on at 30, rim ellipse at
  38 — the spin is back, and in the final the logo rides it glued
  to the coin layer exactly like the old TON glyph did.

### Honest remaining issues
- The big rule has visible costs the user accepted by stating it:
  the logo covers scene subjects in several files (011: boy's
  legs/coin centre; 013: crossed by the gift ribbon per original
  stacking; 017-019: wider than canvas, crop varies by phase;
  009: front phone screen crops the 358px logo to its middle —
  the back-phone carrier shows it whole and readable).
- File 002's logo sits in the old watermark's slot inside the
  "Front 2" layer and is hidden behind the circle in some phases —
  precisely where the old watermark was; it appears when the card
  flips, as the watermark did.
- File 015's main logo is invisible at many frames — inherited from
  the original, whose $SVN mark is equally invisible there
  (render-verified in the audit); the slot's animation is unchanged.
- The trace is a vector approximation of the PNG (IoU 0.983):
  hairline detail of the wordmark is simplified, by design of
  tracing; at emoji size the difference is negligible (IoU 0.959).
- Local build only: NOT published, no Telegram-side rendering or
  upload was performed or claimed.

## v8 — Scale correction: precise contain-fit (2026-10-29)

User verdict on v7 (09:59): the Abolfazl logo is "way too big — the
scales must be measured precisely so it fits in the frame and is
logical in shape, appearance and subject". This SUPERSEDES v7's
always-big rule (>=358px). v8's rule, for every brand element
(main + secondary):

- The logo fits ENTIRELY inside the old logo's on-screen bounding
  box (the median visible box v5/v7 compute: full transform chain,
  visible frames, median), aspect preserved:
  scale = min(box_w/logo_w, box_h/logo_h), centred on the box
  centre, same index/scope/parent and the same animation
  relationship (v5-v7 slot mechanics unchanged).
- Small results are correct now: precision beats size.

Method: v8 does NOT rebuild from originals. `src/emoji_forge/v8.py`
transforms the v7 finals — only each "Abolfazl Logo" group is
re-solved (scripts/build_v8.py). A canonical diff that erases logo
content on both sides proves the non-logo content of every v8
final is IDENTICAL to its v7 final (audit repairs, restored coin
and disc layers, theme recolour, banner disc fix, file 016's
proven-empty comp_1 all preserved). The banner disc slots keep
their stroke-less form (stroke presence is preserved per element
from the v7 group being replaced). Centre handling mirrors v7:
main logo centred on the box centre with v7's minimal on-canvas
clamp (banners 017/019, whose tracked instance centre is
off-canvas); secondaries centred exactly.

### Per-file results (old box -> fitted logo, median frame)

| # | slot | old box | fitted logo |
|---|---|---|---|
| 01 | top | 275.6x250.7 | 275.5x122.2 |
| 02 | comp_1 (group) | 237.0x228.5 | 236.9x147.1 |
| 03 | top (group) | 233.1x233.1 | 232.9x101.3 |
| 04 | comp_0 | 277.2x263.3 | 277.0x134.4 |
| 05 | comp_1 | 327.7x238.3 | 327.5x238.2 |
| 06 | top (group) | 193.3x175.5 | 193.2x84.1 |
| 07 | comp_0 | 243.1x197.7 | 242.9x104.0 |
| 08 | comp_0 | 283.3x257.5 | 283.1x124.7 |
| 09 | comp_1 | 108.2x98.2 | 108.1x47.1 |
| 10 | comp_2 | 204.0x185.2 | 203.9x88.7 |
| 11 | top (group, coin) | 136.7x124.1 | 136.6x59.4 |
| 12 | comp_0 | 113.9x106.2 | 113.8x50.9 |
| 13 | top (group) | 319.8x290.3 | 319.6x139.1 |
| 14 | comp_0 | 201.8x197.3 | 201.7x94.5 |
| 15 | comp_3 main | 166.3x230.1 | 166.2x71.2 |
| 15 | comp_2 secondary | 223.1x195.5 | 222.9x109.8 |
| 16 | comp_0 (comp_1 EMPTY) | 242.2x191.3 | 242.0x107.5 |
| 17 | comp_2 banner | 877.1x129.9 | 298.5x129.9 |
| 17 | comp_3 disc slot | 191.4x173.7 | 191.2x83.2 |
| 18 | comp_2 banner | 877.1x129.9 | 298.5x129.9 |
| 18 | comp_3 disc slot | 191.4x173.7 | 191.2x83.2 |
| 19 | comp_2 banner | 877.1x129.9 | 298.5x129.9 |
| 19 | comp_3 disc slot | 191.4x173.7 | 191.2x83.2 |
| 20 | top | 102.3x93.6 | 102.2x48.0 |

(Fitted sizes bind on the limiting dimension of each box; where
both dimensions bind, e.g. file 005, the layer's own anisotropic
chain scales the old element the same way.)

### Verification
- Canonical non-logo diff vs v7 finals: IDENTICAL on all 20 files.
- Validator: 0 blocked issues on all 20 (1 warning: file 005's own
  4.02s duration, inherited from the original pack).
- Animation fidelity (scripts/fidelity_v8.py, every 5th frame):
  centre deviation <= 0.45px and trajectory deviation <= 0.41px on
  all non-banner files (v7 was up to 68.8px centre on file 011 —
  the fitted logo now rides the coin exactly); rotation and
  opacity deviation 0.0 everywhere. Banners 017/019 keep v7's
  constant clamp shift (464.6/344.2px, trajectory deviation 0.0 —
  pure placement offset, motion identical).
- Containment (logo bbox vs old element bbox per frame): 100% of
  compared frames fully inside on 12 files; worst transient
  overflow 2.7px on files 001/004/007 (rotation of the box between
  the median frame and other frames); file 002 peaks at 13.3px
  during the card flip (its old watermark box rotates/shrinks
  anisotropically; at the median frame the fit is exact).
  Banner containment is measured against the tracked scrolling
  instance whose box sits off-canvas, so it reads as overflow by
  construction; through the on-canvas instances the logo renders
  inside the banner (see strips).
- QA (scripts/qa_v8.py): pack-common signatures 0/20, Abolfazl logo
  present 20/20, blue fraction ~0.000 in 18 files (file 006 flame
  blue by design, file 011 0.008 from the boy's shirt). Banner
  white-disc cleanliness: 0 dark pixels inside the disc interior
  at every 5th frame for files 017/018/019 (original reads 0 as
  control; mask = filled roundish white blobs of the original
  render, eroded — an earlier largest-blob mask misread a text
  fragment as file 018's disc and reported phantom counts).
  Renders: /tmp/v8_qa/file_NN_orig_vs_v8.png, grid
  /tmp/v8_qa/v8_midframe_grid.png (= ~/workspace/v8_midframe_grid.png),
  file 011 coin proof /tmp/v8_qa/file_11_coin_proof.png.
- Tests: 88 run, OK (1 skipped) — 85 baseline + 3 new v8 tests
  (contain-fit scale math, solver never-overflows under identity/
  scaled/rotated matrices, transform fits + preserves non-logo
  content + file 016 comp_1 empty).

### Honest remaining issues
- Fitted logos are small where the old slot was small — that is
  the new rule working as ordered: file 011's logo is 136.6x59.4
  on the coin (it no longer covers the boy), file 020's is
  102.2x48.0 on the ball, file 009's 108.1x47.1 on the phone slot.
- File 015's main logo (166.2x71.2) remains invisible at many
  frames — inherited from the original $SVN slot's own animation.
- File 002's transient 13.3px overflow at flip frames (above).
- Local build only: NOT published, no Telegram-side rendering or
  upload performed or claimed.


## v9 — public release build (access control, bot branding, generic audit, v8 for all users)

Local build only: code + mocked tests. NO Telegram API calls were made,
the running bot was NOT restarted, and no token was used by this build.

### What was built
- Access control (access.py): UserRegistry over the existing per-user
  files (profile + access in one record, atomic writes, in-memory
  cache, one directory scan per process). New users are pending;
  the owner gets an inline Approve/Reject notification; admin
  callbacks are owner-only (non-owners get a refusal callback
  answer). Statuses pending/approved/rejected/blocked; owner always
  approved + unlimited. Defaults for newly approved users:
  3 packs total / 100 emojis total / 1 pack per day (constants in
  access.py; presets default/plus/pro/unlimited). Quota is checked
  BEFORE enqueueing process/publish jobs and counters increment
  ONLY on successful job completion (publish counts only when the
  result starts with "published"; cancelled/failed jobs never
  consume quota). Daily counters keyed by Asia/Tehran date.
  Admin surface: 👥 کاربران menu row (owner only), per-user detail
  with status/quota buttons + preset buttons + free numeric input
  via awaiting state, and commands /users /approve /reject /block
  /unblock /quota /admin.
- Bot branding (branding.py): "Abolfazl" / "Abolfazl • @Abolfazl_rwm" in the
  welcome and help/about texts, ZIP/report captions, and publish
  completion messages. Set-title rule: a user/plan-provided title is
  kept exactly with NO suffix; only the bot-generated default title
  gets " • Abolfazl". Owner-only /apply_branding calls setMyName
  ("Abolfazl | ایموجی‌ساز"), setMyShortDescription,
  setMyDescription and setMyCommands — coded and mock-tested, NOT
  executed. User outputs keep the user's own brand; the admin
  credit toggle (settings.py, default OFF) only adds the standard
  watermark mechanism with "Abolfazl" for users who have not set
  their own watermark.
- Generic audit (audit.py): pack-agnostic classification —
  pack-common geometry is PRIMARY, generic brand-name patterns are
  secondary, theme colour is classified (never deleted). The old
  sVnTeam name lists are no longer deletion rules. The pipeline
  writes audit.json for strip/v8 TGS runs and unions ONLY
  high-confidence logo layer names into the hide set. Bot-level
  audit gate: an automatic full run in strip/v8 with low audit
  confidence is routed to Mati's review queue instead.
- Universal customisation: pipeline mode "v8" for any user —
  logo_vector.trace_logo_image (alpha/corner-colour mask → VTracer
  binary spline trace → the same Lottie asset format as the owner's
  logo) feeds the v7 engine with the user's asset, then v8's
  contain-fit re-solve (BrandProfile.logo_size_mode "fit" DEFAULT,
  "fill" keeps legacy v7 sizing). No logo image → the v5 text path;
  a traced-logo run that placed nothing also falls back to v5, so a
  placeholder name can never leak. Raster items in v8 mode use the
  strip engine; raster text font is selectable (image_engine
  FONT_STYLES: bold_sans/serif/mono, cycled from advanced settings).
  v7/v8 engines were parameterised for arbitrary logo assets with
  default arguments preserving the owner's Abolfazl behaviour exactly.
- Robustness: publish_single_file on the bot (v6 repair plan wired)
  with the validator gate before upload for TGS + 3s spacing +
  retry_after; startup self-check (selfcheck.py, no network) prints
  one [forge] readiness line in run(); queue startup already marks
  leftover "running" jobs interrupted with a Persian reason
  (verified + regression-tested).

### Tests
- 120 run, OK (1 skipped) — 88 baseline + 32 new:
  TestAccessRegistry (10), TestAccessBotGate (7), TestBrandingV9 (4),
  TestAuditGeneric (2), TestPipelineV8Mode (5), TestRobustnessV9 (2),
  TestSingleFilePublishGate (2).
- New coverage highlights: pending user blocked + owner notified with
  buttons; approve via the real callback handler flips status;
  non-owner approve gets a refusal and changes nothing; quota
  refusal at pack/emoji/daily limits; usage recorded only on
  successful completion; generic audit finds a pack-common watermark
  in a synthetic NON-sVnTeam pack and keeps the character; audit
  confidence is low when no shared evidence exists; v8 text and
  traced-logo paths place the brand in-process; a validator-blocked
  TGS never reaches uploadStickerFile in single-file publish.

### Honest limits (v9)
- Nothing here is live-verified: the approval flow and
  /apply_branding are mock-tested only until the deploy steps in
  DEPLOY-FA.md are run (ideally with a second Telegram account).
- User-logo vectorisation is a silhouette: multi-colour logos lose
  internal colours in TGS output (white fill + dark stroke, the
  house readability device). Raster outputs keep the original image.
- The session's default mode remains "strip" (long-standing product
  default); "v8" is a first-class selectable mode, and contain-fit
  is the default logo size mode inside it.
- Pre-v9 non-owner users with an existing profile file normalise to
  "pending" on first read and must be approved once; they do not
  trigger a fresh owner notification (only brand-new records do).
