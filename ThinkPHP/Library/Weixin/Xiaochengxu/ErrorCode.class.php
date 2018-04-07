<?php

namespace Weixin\Xiaochengxu;
class ErrorCode {
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $RequestTokenFailed = -41005;
    public static $SignNotMatch = -41006;
    public static $EncryptDataNotMatch = -41007;
}
