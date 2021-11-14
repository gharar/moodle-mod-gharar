<?php

namespace Gharar\MoodleModGharar;

use core_renderer;
use Gharar\MoodleModGharar\Moodle\Globals;
use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class Util
{
    public static function forbidNonMoodleAccess(): void
    {
        if (!\defined("\MOODLE_INTERNAL")) {
            exit("Access forbidden");
        }
    }

    public static function generateVirtualPhoneNumberFromId(string $id): string
    {
        return "090010" . \str_pad($id, 5, "0", \STR_PAD_LEFT);
    }

    /**
     * @param string|object|array $fields
     * @return string
     */
    public static function getString(
        string $id,
        $fields = null,
        bool $lazyLoad = false
    ): string {
        return \get_string($id, Plugin::COMPONENT_NAME, $fields, $lazyLoad);
    }

    /**
     * @todo Throw exceptions in the case of config name not found?
     * @return mixed|false
     */
    public static function getConfig(string $name)
    {
        return \get_config(Plugin::COMPONENT_NAME, $name);
    }

    public static function getPageRenderer(): core_renderer
    {
        $renderer = Globals::getPage()->get_renderer("core");

        if ($renderer instanceof core_renderer) {
            return $renderer;
        }

        throw new RuntimeException(
            "Expected the renderer from Moodle to be core_renderer"
        );
    }

    public static function jsonEncode($data): string
    {
        return (new JsonEncoder())->encode($data, "");
    }

    public static function jsonDecode(string $data)
    {
        return (new JsonEncoder())->decode($data, "");
    }
}
