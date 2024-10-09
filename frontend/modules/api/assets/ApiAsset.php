<?php

namespace frontend\modules\api\assets;

use yii\web\AssetBundle;

/**
 * Class ApiAsset
 * @package frontend\modules\api\assets
 */
class ApiAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/api/web';

    public $css = [
        'css/api.css',
    ];
    public $js = [
    ];
    public $depends = [

    ];
}