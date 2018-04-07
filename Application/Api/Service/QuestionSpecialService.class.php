<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午5:43
 */

namespace Api\Service;

class QuestionSpecialService extends BaseService {

    /*
     * 这个接口先写死
     */
    public function test ()
    {

        $data = [[["text" => "易错题", "type" => "yclx"]


        ], [

            ["exerTip" => "单", "text" => "单选题", "type" => "dxlx"], ["exerTip" => "判", "text" => "判断题", "type" => "pdlx"

            ], ["exerTip" => "多", "text" => "多选题", "type" => "dclx",], ["exerTip" => "文", "text" => "文字题", "type" => "wzlx"], ["exerTip" => "图", "text" => "图片题", "type" => "tplx"]],

        ];

        return $data;

    }
}