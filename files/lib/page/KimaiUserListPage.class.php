<?php

namespace wcf\page;

use wcf\system\cache\builder\KimaiUserCacheBuilder;
use wcf\system\request\LinkHandler;
use wcf\system\template\TemplateEngine;
use wcf\system\WCF;

class KimaiUserListPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermission = ['admin.kimai.canManageUsers'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.kimai.kimaiUserListPage';

    /**
     * @var \Swagger\Client\Model\UserCollection[]
     */
    protected $users = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        require_once(WCF_DIR . 'lib/system/api/kimai-api-php/autoload.php');
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        $this->users = KimaiUserCacheBuilder::getInstance()->getData();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign('users', $this->users);

        if (WCF::getSession()->getPermission('mod.kimai.canClearUserList')) {
            WCF::getTPL()->assign('resetButton', TemplateEngine::getInstance()->fetch('__kimaiUserListResetButton', 'wcf', [
                'url' => LinkHandler::getInstance()->getLink($this::class)
            ]));
        }
    }
}
