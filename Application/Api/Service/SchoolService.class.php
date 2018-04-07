<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午4:39
 */

namespace Api\Service;
class SchoolService extends BaseService {

    public function getByName ($name)
    {

        if (empty($name)) {
            return NULL;
        }

    }
}