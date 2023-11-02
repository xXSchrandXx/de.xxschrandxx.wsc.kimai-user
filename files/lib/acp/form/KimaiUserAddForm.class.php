<?php

namespace wcf\acp\form;

use Swagger\Client\Model\UserCollection;
use Swagger\Client\Model\UserEntity;
use wcf\data\language\LanguageList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\cache\builder\KimaiUserCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\ColorFormField;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\option\OptionFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\user\UsernameFormField;
use wcf\system\kimai\KimaiApi;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

class KimauUserAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.kimai.kimaiUserAddForm';

    /**
     * @inheritDoc
     */
    public $neededPermission = ['admin.kimai.canManageUsers'];

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = KimaiUserEditForm::class;

    /**
     * Cached user to edit
     * @var \Swagger\Client\Model\UserCollection
     */
    public $formObject;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction === 'edit') {
            $id = 0;
            if (isset($_REQUEST['id'])) {
                $id = intval($_REQUEST['id']);
            }
            foreach (KimaiUserCacheBuilder::getInstance()->getData() as $user) {
                /** @var \Swagger\Client\Model\UserCollection $user */
                if ($user->getId() !== $id) {
                    continue;
                }
                $this->formObject = $user;
                break;
            }
            if (!isset($this->formObject)) {
                throw new IllegalLinkException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        // load language options
        $languageList = new LanguageList();
        $languageList->readObjects();
        $languages = $languageList->getObjects();
        $languageOptions = [];
        foreach ($languages as $language) {
            $languageOptions[] = [
                'label' => $language->__toString(),
                'value' => $language->getFixedLanguageCode(),
                'depth' => 0
            ];
        }

        // load timezone options
        $timezoneOptions = [];
        foreach (DateUtil::getAvailableTimezones() as $timezone) {
            $timezoneOptions[$timezone] = WCF::getLanguage()->get(
                'wcf.date.timezone.' . \str_replace('/', '.', \strtolower($timezone))
            );
        }

        // load supervisor options
        $supervisorOptions = [
            0 => [
                'label' => 'wcf.label.none',
                'value' => 0,
                'depth' => 0
            ]
        ];
        foreach (KimaiUserCacheBuilder::getInstance()->getData() as $user) {
            /** @var \Swagger\Client\Model\UserCollection $user */
            if ($this->formAction === 'edit' && $user->getId() === $this->formObject->getId()) {
                continue;
            }
            $supervisorOptions[] = [
                'label' => $user->getUsername(),
                'value' => $user->getId(),
                'depth' => 0
            ];
        }

        // set fields
        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    UsernameFormField::create()
                        ->autoComplete(null)
                        ->required(),
                    TextFormField::create('alias')
                        ->label('wcf.acp.form.kimaiUserAdd.alias'),
                    TextFormField::create('title')
                        ->label('wcf.acp.form.kimaiUserAdd.title'),
                    IntegerFormField::create('accountNumber')
                        ->label('wcf.acp.form.kimaiUserAdd.accountNumber'),
                    ColorFormField::create('color')
                        ->label('wcf.acp.form.kimaiUserAdd.color'),
                    EmailFormField::create('email')
                        ->label('wcf.acp.form.kimaiUserAdd.email')
                        ->required(),
                    SingleSelectionFormField::create('language')
                        ->label('wcf.acp.form.kimaiUserAdd.language')
                        ->options($languageOptions, true)
                        ->required(),
                    SingleSelectionFormField::create('timzone')
                        ->label('wcf.acp.form.kimaiUserAdd.timezone')
                        ->options($timezoneOptions)
                        ->required(),
                    SingleSelectionFormField::create('supervisor')
                        ->label('wcf.acp.form.kimaiUserAdd.supervisor')
                        ->options($supervisorOptions, true),
                    MultipleSelectionFormField::create('roles')
                        ->label('wcf.acp.form.kimaiUserAdd.supervisor')
                        ->options([
                            0 => [
                                'label' => 'wcf.acp.form.kimaiUserAdd.supervisor.role.teamlead',
                                'value' => 'ROLE_TEAMLEAD',
                                'depth' => 0
                            ],
                            1 => [
                                'label' => 'wcf.acp.form.kimaiUserAdd.supervisor.role.admin',
                                'value' => 'ROLE_ADMIN',
                                'depth' => 0
                            ],
                            2 => [
                                'label' => 'wcf.acp.form.kimaiUserAdd.supervisor.role.superadmin',
                                'value' => 'ROLE_SUPER_ADMIN',
                                'depth' => 0
                            ]
                        ], true),
                    PasswordFormField::create('plainPassword')
                        ->label('wcf.acp.form.kimaiUserAdd.plainPassword')
                        ->required(),
                    TextFormField::create('plainApiToken')
                        ->label('wcf.acp.form.kimaiUserAdd.plainApiToken'),
                    BooleanFormField::create('enabled')
                        ->label('wcf.acp.form.kimaiUserAdd.enabled'),
                    BooleanFormField::create('systemAccount')
                        ->label('wcf.acp.form.kimaiUserAdd.systemAccount'),
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        if (!empty($_POST) || !empty($_FILES)) {
            $this->submit();
        }

        // call readData event
        EventHandler::getInstance()->fireAction($this, 'readData');

        $parameters = [];
        if (isset($this->formObject)) {
            $parameters['mail_login'] = $this->formObject['mail_login'];
        }
        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        // call save event
        EventHandler::getInstance()->fireAction($this, 'save');

        $formData = $this->form->getData();
        if (!isset($formData['data'])) {
            $formData['data'] = [];
        }
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);

        // modify formObject
        if ($this->formAction === 'create') {
            $this->formObject = new UserCollection();
        } else {

        }

        try {
            $api = new KimaiApi();
            if ($this->formAction === 'create') {
                $api->getUserApi()->postPostUser($this->formObject->__toString());
            } else {
                $api->getUserApi()->patchPatchUser($this->formObject->__toString(), $this->formObject->getId());
            }
        } catch (\Swagger\Client\ApiException $e) {
            WCF::getTPL()->assign([
                'faultCode' => $e->getCode(),
                'faultString' => $e->getResponseBody()
            ]);
            return;
        }

        $this->saved();

        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        parent::saved();

        KimaiUserCacheBuilder::getInstance()->reset();
    }
}
