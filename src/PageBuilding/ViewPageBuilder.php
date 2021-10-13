<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use cm_info;
use html_writer;
use context_module;
use MAChitgarha\MoodleModGharar\Capability;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableLiveMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableRoomMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\AuthToken;
use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;

class ViewPageBuilder extends AbstractPageBuilder
{
    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $instanceId;

    /** @var \stdClass */
    private $course;

    /** @var cm_info */
    private $moduleInfo;

    /** @var object */
    private $instance;

    /** @var context_module */
    private $context;

    /** @var API */
    private $api;

    /** @var AvailableRoom */
    private $roomInfo;

    /** @var AuthToken */
    private $authToken;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo()
            ->initInstance()
            ->initContext()
            ->requireLogin()
            ->initAPI()
            ->initRoomInfo();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function initCourseAndModuleInfo(): self
    {
        [
            $this->course,
            $this->moduleInfo
        ] = \get_course_and_cm_from_cmid(
            $this->instanceId,
            Plugin::MODULE_NAME
        );

        return $this;
    }

    private function initInstance(): self
    {
        $this->instance = Globals::getDatabase()
            ->get_record(
                Database::TABLE_MAIN,
                ["id" => $this->moduleInfo->instance],
                "*",
                \MUST_EXIST
            );

        return $this;
    }

    private function initContext(): self
    {
        $this->context = context_module::instance($this->instanceId);

        return $this;
    }

    private function requireLogin(): self
    {
        \require_login($this->course, true, $this->moduleInfo);

        return $this;
    }

    private function initAPI(): self
    {
        $this->api = new API(
            Util::getConfig(AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME)
        );

        return $this;
    }

    private function initRoomInfo(): self
    {
        $this->roomInfo = $this->api->retrieveRoom(
            $this->instance->address
        );

        return $this;
    }

    protected function prepare(): self
    {
        if ($this->isCurrentUserNonAdminHavingLive()) {
            $this->prepareNonAdminLiveMember();
        } else {
            $this->prepareRoomMember();
        }

        return $this;
    }

    private function prepareNonAdminLiveMember(): self
    {
        $user = Globals::getUser();

        $liveMember = new AvailableLiveMember(
            Util::generateVirtualPhoneNumberFromId($user->id)
        );
        $liveMember->setName(
            "{$user->firstname} {$user->lastname}"
        );

        if (!$this->api->hasLiveMember(
            $this->instance->address,
            $liveMember->getPhone()
        )) {
            $liveMember = $this->api->createLiveMember(
                $this->instance->address,
                $liveMember
            );
        }

        $this->authToken = $this->api->generateAuthToken($liveMember);

        return $this;
    }

    private function prepareRoomMember(): self
    {
        $user = Globals::getUser();

        $roomMember = new AvailableRoomMember(
            Util::generateVirtualPhoneNumberFromId($user->id),
            $this->isCurrentUserRoomAdmin()
        );
        $roomMember->setName(
            "{$user->firstname} {$user->lastname}"
        );

        if ($this->api->hasRoomMember(
            $this->instance->address,
            $roomMember->getPhone()
        )) {
            $roomMember = $this->api->updateRoomMember(
                $this->instance->address,
                $roomMember
            );
        } else {
            $roomMember = $this->api->createRoomMember(
                $this->instance->address,
                $roomMember
            );
        }

        $this->authToken = $this->api->generateAuthToken($roomMember);

        return $this;
    }

    private function isCurrentUserRoomAdmin(): bool
    {
        return has_capability(
            Capability::ENTER_ROOM_AS_ADMIN,
            $this->context
        );
    }

    private function isCurrentRoomHavingLive(): bool
    {
        return $this->roomInfo->hasLive();
    }

    private function isCurrentUserNonAdminHavingLive(): bool
    {
        return
            $this->isCurrentRoomHavingLive() &&
            !$this->isCurrentUserRoomAdmin();
    }

    protected function buildPage(): self
    {
        $page = Globals::getPage();

        $page->set_url(self::URL, ["id" => $this->instanceId]);
        $page->set_title(
            "{$this->course->shortname}: {$this->instance->name}"
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);

        return $this;
    }

    protected function generateOutputHeading(): string
    {
        return $this->instance->name;
    }

    protected function generateOutputMainContent(): string
    {
        if (!$this->isCurrentRoomHavingLive()) {
            $url = implode([
                $this->roomInfo->getShareUrl(),
                "?jwt=",
                $this->authToken->getToken(),
            ]);
            $string = "enter_room";
        } else {
            $url = implode([
                $this->roomInfo->getLiveUrl(),
                "?token=",
                $this->authToken->getToken(),
            ]);
            $string = "enter_live";
        }

        return html_writer::link(
            $url,
            Util::getString($string),
            ["target" => "_blank"]
        );
    }
}
