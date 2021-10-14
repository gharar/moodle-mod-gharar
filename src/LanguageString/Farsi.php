<?php

namespace MAChitgarha\MoodleModGharar\LanguageString;

class Farsi
{
    private const PLUGIN_NAME = "قرار";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "ها";

    public const STRING = [
        // Moodle-specific {
        "modulename" => self::PLUGIN_NAME,
        "pluginname" => self::PLUGIN_NAME,
        "modulenameplural" => self::PLUGIN_NAME_PLURAL,

        "pluginadministration" => "مدیریت " . self::PLUGIN_NAME,

        "gharar:addinstance" => "افزودن فعالیت قرار",
        "gharar:view" => "مشاهده‌ی فعالیت قرار",
        "gharar:enter_room_as_admin" => "ورود به اتاق قرار به عنوان مدیر",
        // TODO: Add modulename_help
        // }

        "plugin_name" => self::PLUGIN_NAME,
        "plugin_name_plural" => self::PLUGIN_NAME_PLURAL,

        "instance_data_form_field_name" => "نام",
        "instance_data_form_field_room_name" => "نام اتاق",
        "instance_data_form_field_is_private" => "خصوصی",
        "instance_data_form_block_room_settings" => "تنظیمات اتاق",

        "access_token" => "توکن دسترسی",
        "access_token_description" => "کد خصوصی یکتای دسترسی به قرار",

        "enter_room" => "ورود به کلاس مجازی",
        // TODO: Change this
        "enter_live" => "ورود به کلاس مجازی",

        "error_api_request_timeout" => "زمان درخواست به سرورهای قرار از حد " .
            "انتظار فراتر رفت. دوباره تلاش کنید.",
        "error_api_request_unauthorized" => "دسترسی غیرمجاز به سرورهای قرار؛ " .
            "احتمالا به خاطر توکن دسترسی نادرست یا منقضی‌شده.",
        "error_api_request_unhandled" => "خطای مدیریت‌نشده. پیام خطا: " .
            "{\$a->message}؛ کد وضعیت: {\$a->statusCode}",
        "error_api_request_duplicated_room_name" => "نام اتاق تکراری است (" .
            "اتاقی با این نام از قبل هست).",
    ];
}
