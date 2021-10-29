<?php

namespace Gharar\MoodleModGharar\LanguageString;

class Farsi
{
    private const PLUGIN_NAME = "قرار";
    private const PLUGIN_NAME_PLURAL = self::PLUGIN_NAME . "ها";

    public const STRING = [
        StringId::MODULE_NAME => self::PLUGIN_NAME,
        StringId::PLUGIN_NAME => self::PLUGIN_NAME,
        StringId::MODULE_NAME_PLURAL => self::PLUGIN_NAME_PLURAL,
        StringId::PLUGIN_NAME_PLURAL => self::PLUGIN_NAME_PLURAL,

        StringId::PLUGIN_ADMINISTRATION => "مدیریت " . self::PLUGIN_NAME,

        StringId::CAPABILITY_ADD_INSTANCE => "افزودن فعالیت قرار",
        StringId::CAPABILITY_VIEW_INSTANCE => "مشاهده‌ی فعالیت قرار",
        StringId::CAPABILITY_ROOM_ADMIN => "مدیر اتاق‌ها بودن",

        StringId::FORM_INSTANCE_FIELD_NAME => "نام",
        StringId::FORM_INSTANCE_FIELD_ROOM_NAME => "نام اتاق",
        StringId::FORM_INSTANCE_FIELD_IS_PRIVATE => "خصوصی",
        StringId::FORM_INSTANCE_FIELD_ROLES_CAN_VIEW_RECORDINGS =>
            "نمایش برای (نقش‌های)",
        StringId::FORM_INSTANCE_BLOCK_ROOM_SETTINGS => "تنظیمات اتاق",
        StringId::FORM_INSTANCE_BLOCK_RECORDINGS => "ضبط‌شده‌ها",

        StringId::CONFIG_ACCESS_TOKEN => "توکن دسترسی",
        StringId::CONFIG_ACCESS_TOKEN_DESCRIPTION =>
            "کد خصوصی یکتای دسترسی به قرار",

        StringId::PAGE_VIEW_ENTER_ROOM => "ورود",
        StringId::PAGE_VIEW_ENTER_ROOM_HAVING_LIVE =>
            "ورود به اتاق ارائه‌دهندگان",
        StringId::PAGE_VIEW_ENTER_LIVE => "ورود به صفحه‌ی بینندگان",
        StringId::PAGE_VIEW_NO_RECORDINGS_AVAILABLE => "موردی یافت نشد.",
        StringId::PAGE_VIEW_HEADING_RECORDINGS => "ضبط‌شده‌ها",

        StringId::ERROR_API_TIMEOUT => "زمان درخواست به سرورهای قرار از حد " .
            "انتظار فراتر رفت. دوباره تلاش کنید.",
        StringId::ERROR_API_UNAUTHORIZED => "دسترسی غیرمجاز به سرورهای قرار. " .
            "مطمئن شوید که توکن دسترسی درست و منقضی‌نشده است.",
        StringId::ERROR_API_UNHANDLED => "خطای مدیریت‌نشده. پیام خطا: " .
            "{\$a->message}؛ کد وضعیت: {\$a->statusCode}",
        StringId::ERROR_API_DUPLICATED_ROOM_NAME => "نام اتاق تکراری است (" .
            "اتاقی با این نام از قبل هست).",
    ];
}
