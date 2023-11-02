<?php

namespace wcf\action;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use wcf\page\KimaiUserListPage;
use wcf\action\AbstractAction;
use wcf\system\cache\builder\KimaiUserCacheBuilder;
use wcf\system\request\LinkHandler;

final class KimaiUserListResetAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermission = ['mod.kimai.canClearUserList'];

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        KimaiUserCacheBuilder::getInstance()->reset();

        $this->executed();

        if (isset($_REQUEST['noRedirect']) || isset($_POST['noRedirect'])) {
            return new EmptyResponse();
        } else if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
            return new RedirectResponse(
                $_REQUEST['url']
            );
        } else {
            return new RedirectResponse(
                LinkHandler::getInstance()->getControllerLink(KimaiUserListPage::class)
            );
        }
    }
}
