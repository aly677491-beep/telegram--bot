<?php
// ========== تفعيل عرض الأخطاء للتصحيح ==========
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ========== استدعاء ملف البيانات ==========
require "data.php";

// ========== قراءة التحديثات من تيليجرام ==========
$update = json_decode(file_get_contents('php://input'), true);

// ========== تهيئة المتغيرات ==========
$message = null;
$callback_query = null;
$data = null;
$id = null;
$chat_id = null;
$text = null;
$user_name = null;
$name = null;
$message_id = null;
$type = null;

if (isset($update['message'])) {
    $message = $update['message'];
    $id = $message['from']['id'] ?? null;
    $chat_id = $message['chat']['id'] ?? null;
    $text = $message['text'] ?? null;
    $user_name = $message['from']['username'] ?? null;
    $name = $message['from']['first_name'] ?? null;
    $message_id = $message['message_id'] ?? null;
    $type = $message['chat']['type'] ?? null;
}

if (isset($update['callback_query'])) {
    $callback_query = $update['callback_query'];
    $data = $callback_query['data'] ?? null;
    $id = $callback_query['from']['id'] ?? null;
    $chat_id = $callback_query['message']['chat']['id'] ?? null;
    $user_name = $callback_query['from']['username'] ?? null;
    $name = $callback_query['from']['first_name'] ?? null;
    $message_id = $callback_query['message']['message_id'] ?? null;
    $type = $callback_query['message']['chat']['type'] ?? null;
}

// ========== تفعيل Webhook ==========
$link = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
file_get_contents("https://api.telegram.org/bot$token/setWebHook?url=$link");

// ========== معالجة البيانات ==========
$ex = isset($data) ? explode("#", $data) : [];

// ========== التحقق من صلاحيات الأدمن ==========
if ($id == $admin) {
    
    // ===== القائمة الرئيسية =====
    if ($text == "/start" || $data == "back") {
        $info["admin"] = "";
        save();
        
        if ($data == "back") {
            $bot->deleteMessage([
                "chat_id" => $chat_id,
                "message_id" => $message_id
            ]);
        }
        
        $bot->sendMessage([
            "chat_id" => $chat_id,
            "text" => trim($txt["القائمة الرئيسية"]),
            "reply_markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "⛔ ⁞ حذف دولة .", "callback_data" => "del"],
                        ["text" => "✅ ⁞ اضف دولة .", "callback_data" => "add"]
                    ],
                    [
                        ["text" => "🌎 ⁞ عرض الدول المضافة .", "callback_data" => "all"]
                    ],
                    [
                        ["text" => "⚜️ ⁞ اضافة معلوماتك .", "callback_data" => "addinformation"],
                        ["text" => "♻️ ⁞ فحص حالة الصيد .", "callback_data" => "to_examine"]
                    ]
                ]
            ])
        ]);
    }
    
    // ===== تشغيل الصيد =====
    elseif ($text == "/work") {
        $bot->sendMessage([
            "chat_id" => $chat_id,
            "text" => trim($txt["تشغيل الصيد"])
        ]);
        $info["status"] = "work";
        save();
    }
    
    // ===== إيقاف الصيد =====
    elseif ($text == "/stop") {
        $bot->sendMessage([
            "chat_id" => $chat_id,
            "text" => trim($txt["ايقاف الصيد"])
        ]);
        $info["status"] = null;
        save();
    }
    
    // ===== معالجة الضغط على الأزرار =====
    elseif ($data) {
        
        // عرض الدول المضافة
        if ($data == "all") {
            $all = isset($info["countries"]) ? implode("\n", $info["countries"]) : "📣 ⁞ قم بإضافة دول لاتوجد دول مضافة .";
            $bot->answerCallbackQuery([
                "callback_query_id" => $callback_query['id'] ?? null,
                "text" => $all,
                "show_alert" => true,
            ]);
        }
        
        // فحص الحالة
        elseif ($data == "to_examine") {
            $check = json_decode(file_get_contents("http://api.durianrcs.com/out/ext_api/getUserInfo?name=$user&pwd=$pass&ApiKey=$api_key"), true);
            $code = $check["code"] ?? null;
            if ($code == 200) {
                $examine = "شغال طبيعي ✅";
            } else {
                $examine = "اسم المستخدم او كلمة المرور غير صحيح ⚠️";
            }
            $bot->answerCallbackQuery([
                "callback_query_id" => $callback_query['id'] ?? null,
                "text" => $examine,
                "show_alert" => true,
            ]);
        }
        
        // إضافة المعلومات
        elseif ($data == "addinformation") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "⚙️ ⁞ قم برفع معلومات من الاسفل ⬇️",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "💠 ⁞ رفع الــ api", "callback_data" => "add_api"]],
                        [["text" => "💠 ⁞ رفع اسم المستخدم", "callback_data" => "add_username"]],
                        [["text" => "💠 ⁞ رفع الباسوورد", "callback_data" => "add_password"]],
                        [["text" => "💠 ⁞ رفع التطبيق", "callback_data" => "add_app"]],
                        [["text" => "💠 ⁞ رفع قناة الصيد", "callback_data" => "add_chaneel"]],
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
        }
        
        // إضافة API
        elseif ($data == "add_api") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بإرسال ال api الخاص بك",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add_api";
            save();
        }
        
        // إضافة اسم المستخدم
        elseif ($data == "add_username") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بإرسال الإسم المستخدم الخاص بك",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add_username";
            save();
        }
        
        // إضافة الباسوورد
        elseif ($data == "add_password") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بإرسال الباسوورد الخاص بك",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add_password";
            save();
        }
        
        // إضافة التطبيق
        elseif ($data == "add_app") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بإرسال رمز التطبيق للصيد.\n✅ ⁞ رقم تطبيق الواتساب 0107 \n✅ ⁞ رقم تطبيق التلجرام 0257",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add_app";
            save();
        }
        
        // إضافة قناة الصيد
        elseif ($data == "add_chaneel") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بإرسال ايدي قناة الصيد",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add_chaneel";
            save();
        }
        
        // إضافة دولة
        elseif ($data == "add") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بارسال رمز الدولة في موقع البوت ورموز الدول هي الآتي ⬇️\nالكويت: kw، تايلاند: th\nالجزائر: dz، المغرب: ma\nليبيا: ly، انغولا: ao\nاليمن: ye، فنزويلا: ve\nامريكا: us، بريطانا: gb\nالامارات: ae، البحرين: bh\nتركيا: tr، تونس: tn\nعمان: om، استراليا: au\nالنمسا: at، كولموبيا: co\nالمانيا: de، ايرلندا: ie\nاليابان: jp، الاردن: jo\nكينيا: ke، هولاندا: nl\nالنرويج: no، البرتغال: pr\nالسعودية: sa، اسبانيا: es\nجنوب افريقيا: za، سويسرا: ch\nمصر: eg، امريكا: us\nسامو: ws، فانواتو: vu\nسوازلاند: sz، سوريا: sy\nالسويد: se، جزر سليمان: sb\nبوريتكو يجيب ارقام امريكي: pr\nبابوغينا الجديده pg، نويزلاند: nz\nناورو: nr، ايطاليا: it\nجزر المادليف: mv، موريتانيا: mr\nايسلاند: is، لبنان: lb\nكيريباتي: ki، كوريا الجنوبية: lr\nكوريا الشماليه: kp، كمبوديا: kh\nالعراق iq، اليونان: gr\nجزر فوكلاند: fk، فيجي: fj\nالدنمارك: dk، قبرص: cy\nجزر الكوك: ck، بلجيكا: be",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "add";
            save();
        }
        
        // حذف دولة
        elseif ($data == "del") {
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => "🔰 ⁞ رجاءً قم بارسال كود الدولة",
                "message_id" => $message_id,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "رجوع للخلف ⬅️", "callback_data" => "back"]],
                    ]
                ])
            ]);
            $info["admin"] = "del";
            save();
        }
    }
    
    // ===== استقبال النصوص من الأدمن =====
    elseif ($text && isset($info["admin"])) {
        
        if ($info["admin"] == "add_api") {
            $json["api_key"] = $text;
            server();
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تم إضافة ال api بنجاح"
            ]);
        }
        
        elseif ($info["admin"] == "add_username") {
            $json["username"] = $text;
            server();
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تم إضافة إسم المستخدم بنجاح"
            ]);
        }
        
        elseif ($info["admin"] == "add_password") {
            $json["password"] = $text;
            server();
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تم إضافة الباسوورد بنجاح"
            ]);
        }
        
        elseif ($info["admin"] == "add_app") {
            $json["app"] = $text;
            server();
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تم إضافة التطبيق $text بنجاح"
            ]);
        }
        
        elseif ($info["admin"] == "add_chaneel") {
            $chaneel = "-100$text";
            $json["chaneel"] = $chaneel;
            server();
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تم إضافة ايدي قناة الصيد بنجاح"
            ]);
        }
        
        elseif ($info["admin"] == "add") {
            $code = uniqid();
            $info["countries"][$code] = $text;
            $info["admin"] = "";
            save();
            $bot->sendMessage([
                "chat_id" => $chat_id,
                "text" => "✅ ⁞ تمت الاضافة بنجاح\nكود الدولة\n$code\nيستخدم هذا الكود عند الرغبة بحذف الدولة"
            ]);
        }
        
        elseif ($info["admin"] == "del") {
            if (!isset($info["countries"][$text])) {
                $bot->sendMessage([
                    "chat_id" => $chat_id,
                    "text" => "❌ ⁞ لاتوجد دولة مضافة بهذا الكود تأكد من صحة الكود"
                ]);
                $info["admin"] = "";
                save();
            } else {
                unset($info["countries"][$text]);
                $info["admin"] = "";
                save();
                $bot->sendMessage([
                    "chat_id" => $chat_id,
                    "text" => "✅ ⁞ تم الحذف بنجاح"
                ]);
            }
        }
    }
}

// ========== معالجة طلب الكود ==========
if (!empty($ex) && $ex[0] == "getCode") {
    if (isset($ex[2]) && isset($ex[1])) {
        $res = $api->getCode($ex[2], $ex[1]);
        if (empty($res["Error"]) && isset($res["code"]) && $res["code"] != 0) {
            $code = $res["code"];
            $idd = $callback_query['from']['id'] ?? null;
            
            $bot->editMessageText([
                "chat_id" => $chat_id,
                "text" => trim(str_replace([
                    "__code__", "__number__"
                ], [
                    $code, $ex[2]
                ], $txt["الكود"])),
                "parse_mode" => "markdown",
                "message_id" => $message_id
            ]);
            
            if (isset($Customer[$idd]['add'])) {
                $Customer[$idd]['add'] -= 1;
                servd();
                
                if ($Customer[$idd]['add'] <= 1) {
                    sleep(15);
                    $bot->kickChatMember([
                        "chat_id" => $chat_id,
                        "user_id" => $idd
                    ]);
                    
                    $bot->sendMessage([
                        'chat_id' => $admin,
                        'text' => "
🤖 *⁞ قام هذا الشخص بسحب رقم*
 
 🧑🏻‍💼 ⁞ الشخص : [$name](tg://user?id=$idd)
",
                        "parse_mode" => "markdown",
                        'disable_web_page_preview' => true,
                    ]);
                    
                    unset($Customer[$idd]);
                    servd();
                }
            }
        } else {
            $bot->answerCallbackQuery([
                "callback_query_id" => $callback_query['id'] ?? null,
                "text" => "🚫 لم يصل الكود",
                "show_alert" => true,
            ]);
        }
    }
}

// ========== معالجة حظر الرقم ==========
elseif (!empty($ex) && $ex[0] == "ban") {
    if (isset($ex[2]) && isset($ex[1])) {
        $res = $api->banNum($ex[2], $ex[1]);
        $bot->editMessageText([
            "chat_id" => $chat_id,
            "text" => "⚠️ *⁞ تم حظر الرقم بنجاح .*",
            "message_id" => $message_id
        ]);
    }
}
?>
