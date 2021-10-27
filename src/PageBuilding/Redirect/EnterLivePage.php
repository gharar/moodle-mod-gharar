<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Redirect;

use MAChitgarha\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableLiveMember;

class EnterLivePage
{
    use Traits\PageBuilderTrait,
        BaseTraits\MoodleConfigLoaderTrait,
        BaseTraits\CourseAndModuleInfoInitializerTrait,
        BaseTraits\InstanceInitializerTrait,
        BaseTraits\ApiInitializerTrait,
        BaseTraits\RoomInfoInitializerTrait;

    use BaseTraits\RequireLoginTrait {
        requireCourseModuleLogin as requireLogin;
    }

    public const RELATIVE_URL = Base::RELATIVE_PATH . "/enter-live.php";

    /** @var int */
    private $instanceId;

    /** @var AuthToken */
    private $authToken;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo($this->instanceId, Plugin::MODULE_NAME)
            ->requireLogin($this->course, $this->moduleInfo)
            ->initInstance($this->moduleInfo)
            ->initApi()
            ->initRoomInfo($this->api, $this->instance->address)
            ->validateRoomHavingLive();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function validateRoomHavingLive(): self
    {
        // TODO: Improve error message
        if (!$this->roomInfo->hasLive()) {
            throw new \Exception();
        }

        return $this;
    }

    protected function prepare(): self
    {
        // TODO: See if the room is active or not.

        $this->ensurePresentLiveMember(
            $liveMember = $this->generateLiveMember(
                Globals::getUser()
            )
        );

        $this->authToken = $this->api->generateAuthToken($liveMember);

        return $this;
    }

    private function generateLiveMember(stdClass $user): AvailableLiveMember
    {
        $liveMember = new AvailableLiveMember(
            Util::generateVirtualPhoneNumberFromId($user->id)
        );
        $liveMember->setName(
            "{$user->firstname} {$user->lastname}"
        );

        return $liveMember;
    }

    private function ensurePresentLiveMember(
        AvailableLiveMember $liveMember
    ): self {
        if (!$this->api->hasLiveMember(
            $this->instance->address,
            $liveMember->getPhone()
        )) {
            $liveMember = $this->api->createLiveMember(
                $this->instance->address,
                $liveMember
            );
        }

        return $this;
    }

    protected function configure(): self
    {
        $page = Globals::getPage();

        $page->set_url(self::RELATIVE_URL, ["id" => $this->instanceId]);
        $page->set_title(
            "{$this->course->shortname}: {$this->instance->name}"
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);

        return $this;
    }

    public function generateRedirectionLocation(): string
    {
        return \implode([
            $this->roomInfo->getLiveUrl(),
            "?token=",
            $this->authToken->getToken(),
        ]);
    }
}