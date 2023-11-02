<?php

namespace wcf\acp\form;

class KimaiUserEditForm extends KimauUserAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.kas.kasMailPage';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';
}
