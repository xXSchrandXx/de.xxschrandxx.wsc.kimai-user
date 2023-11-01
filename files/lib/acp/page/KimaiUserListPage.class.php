<?php

namespace wcf\acp\page;

use Fiteco\KimaiClient\Model\UserCollection;
use wcf\page\AbstractPage;
use wcf\system\cache\builder\KimaiUserCacheBuilder;
use wcf\system\WCF;

class KimaiUserListPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.kimai.kimaiUserListPage';

    /**
     * @inheritDoc
     */
    public $neededPermission = ['admin.kimai.canManageUsers'];

    /**
     * @var UserCollection[]
     */
    protected $users = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        $this->users = KimaiUserCacheBuilder::getInstance()->getData();
        wcfDebug($this->users);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'users' => $this->users
        ]);
    }
}
