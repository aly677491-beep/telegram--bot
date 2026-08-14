<?php
// ========== ملف البيانات والإعدادات ==========

// قراءة ملفات JSON
$info = json_decode(file_get_contents("info.json"),1);
$json = json_decode(file_get_contents("json.json"),1);
$Customer = json_decode(file_get_contents("Customer.json"),1);

// ========== دوال الحفظ ==========
function save(){
    global $info;
    if(! empty ($info)) 
    file_put_contents("info.json",json_encode($info,448));
}

function server(){
    global $json;
    if(! empty ($json)) 
    file_put_contents("json.json",json_encode($json,448));
}

function servd(){
    global $Customer;
    if(! empty ($Customer)) 
    file_put_contents("Customer.json",json_encode($Customer,448));
}

// ========== إعدادات البوت ==========
$token = "8597336903:AAFD-skzlnbYH1q8M_nIMmUS7Q7Dy_CxU48"; // TOKEN الخاص بك
$ch = $json["chaneel"]; // ايدي قناة الصيد
$admin = 7124462252; // ايديك في تيليجرام
$api_key = $json["api_key"]; // API Key
$user = $json["username"]; // اسم المستخدم
$pass = $json["password"]; // كلمة السر
$app = $json["app"]; // التطبيق

// ========== استدعاء الملفات ==========
require "class.php";
require "Telegram.php";

// ========== إنشاء الكائنات ==========
$bot = new Telegram ($token);
$api = new MainClass($user,$pass,$app,$api_key);

// ========== النصوص ==========
// ملف الصيد
$txt["رسالة الصيد"] =
"
*☑️ ⁞ تم شراء رقم جديد*

*☎️ ⁞ الرقم : __number__*
";

$txt["حظر الرقم"] = "
⚠️ ⁞ حظر الرقم .
";

$txt["طلب الكود"] = "
✳️ ⁞ طلب الكود .
";

// ملف التحكم
$txt["القائمة الرئيسية"]="
/work لجعل البوت يبدا الصيد
/stop لجعل البوت يتوقف عن الصيد

❇️ ⁞ ملاحظة :
⬅️ عند ايقاف الصيد لا يتوقف مباشرة وانما يتوقف بعد مرور دقيقة ➡️
";

$txt["تشغيل الصيد"] ="
✅ ⁞ تم تشغيل الصيد .
";

$txt["ايقاف الصيد"] ="
✅ ⁞ تم ايقاف الصيد .
";

$txt["الكود"]="
✅ ⁞ تم وصول الكود بنجاح
☎️ ⁞ الرقم : `__number__`
📩 ⁞ الكود : `__code__`
";
// قائمة الدول مع الأعلام
$country_flags = [
    'sa' => '🇸🇦',
    'ae' => '🇦🇪',
    'eg' => '🇪🇬',
    'kw' => '🇰🇼',
    'qa' => '🇶🇦',
    'om' => '🇴🇲',
    'bh' => '🇧🇭',
    'dz' => '🇩🇿',
    'ma' => '🇲🇦',
    'ly' => '🇱🇾',
    'ye' => '🇾🇪',
    'tr' => '🇹🇷',
    'tn' => '🇹🇳',
    'us' => '🇺🇸',
    'gb' => '🇬🇧',
    'de' => '🇩🇪',
    'fr' => '🇫🇷',
    'it' => '🇮🇹',
    'es' => '🇪🇸',
    'jp' => '🇯🇵',
    'kr' => '🇰🇷',
    'cn' => '🇨🇳',
    'ru' => '🇷🇺',
    'br' => '🇧🇷',
    'in' => '🇮🇳',
    'pk' => '🇵🇰',
    'id' => '🇮🇩',
    'my' => '🇲🇾',
    'th' => '🇹🇭',
    'vn' => '🇻🇳',
    'ph' => '🇵🇭'
];

// اسماء الدول بالعربية
$country_names = [
    'sa' => 'السعودية',
    'ae' => 'الإمارات',
    'eg' => 'مصر',
    'kw' => 'الكويت',
    'qa' => 'قطر',
    'om' => 'عمان',
    'bh' => 'البحرين',
    'dz' => 'الجزائر',
    'ma' => 'المغرب',
    'ly' => 'ليبيا',
    'ye' => 'اليمن',
    'tr' => 'تركيا',
    'tn' => 'تونس',
    'us' => 'أمريكا',
    'gb' => 'بريطانيا',
    'de' => 'ألمانيا',
    'fr' => 'فرنسا',
    'it' => 'إيطاليا',
    'es' => 'إسبانيا',
    'jp' => 'اليابان',
    'kr' => 'كوريا',
    'cn' => 'الصين',
    'ru' => 'روسيا',
    'br' => 'البرازيل',
    'in' => 'الهند',
    'pk' => 'باكستان',
    'id' => 'إندونيسيا',
    'my' => 'ماليزيا',
    'th' => 'تايلاند',
    'vn' => 'فيتنام',
    'ph' => 'الفلبين'
];

// دالة لجلب اسم الدولة
function getCountryName($code) {
    global $country_names;
    return $country_names[$code] ?? $code;
}

// دالة لجلب علم الدولة
function getCountryFlag($code) {
    global $country_flags;
    return $country_flags[$code] ?? '🌍';
}
?>
