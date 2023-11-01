<?php

namespace wcf\system\cache\builder;

use wcf\system\kimai\KimaiApi;

class KimaiUserCacheBuilder extends AbstractCacheBuilder
{
    public function rebuild(array $parameters)
    {
        $data = [];
        try {
            $api = new KimaiApi();
            $data = $api->getUserApi()->getGetUsers();
        } catch (\Swagger\Client\ApiException $e) {
            \wcf\functions\exception\logThrowable($e);
        }
        return $data;
    }
}
