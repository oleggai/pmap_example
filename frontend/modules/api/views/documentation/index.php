<?php

/**
 * @var $this \yii\web\View
 */

\yii\bootstrap\BootstrapAsset::register($this);

\frontend\modules\api\assets\ApiAsset::register($this);

$accessToken = '59444c51-1c47-4ae8-b018-da0357362293';

$baseApiUrl = 'https://pmap.minregion.gov.ua/index.php?r=api/document&access-token='.$accessToken;

$techEmail = \yii\helpers\Html::mailto('o.pilat@brdo.com.ua', null, ['href' => 'mailto:o.pilat@brdo.com.ua']);

$fields = [
    'uuid' => 'Ідентифікатор документу',
    'ato_type' => 'Тип АТО',
    'ato_name' => 'Назва АТО',
    'koatuu' => 'КОАТУУ',
    'document_type' => 'Тип документу ('. implode('; ', \yii\helpers\ArrayHelper::getColumn(\common\models\TypeDocument::find()->all(), 'name')) .')',
    'name' => 'Назва рішення про затвердження',
    'date_approval' => 'Дата затвердження',
    'decision_number' => 'Номер рішення про затвердження',
    'link_decision' => 'Лінк',
    'link_text' => 'Лінк 2',
    'link_graphic' => 'Лінк 3',
    'percentage_coverage' => 'Відсоток покриття',
    'info_territory' => 'Інформація про територію ДПТ',
    'zoning_plan' => 'План зонування території у складі генерального плану',
    'additional_info' => 'Додаткова інформація',
    'updated_at' => 'Дата оновлення документу'
];

$headers = [
        'x-pagination-current-page' => 'Поточна сторінка',
    'x-pagination-page-count' => 'Загальна кількість сторінок',
    'x-pagination-per-page' => 'Кількість записів на сторінці',
    'x-pagination-total-count' => 'Загальна кількість записів',
    'x-rate-limit-limit' => 'Максимальна кількість запитів, дозволена протягом періоду часу',
    'x-rate-limit-remaining' => 'Кількість запитів, яка залишилась в поточному періоді часу',
    'x-rate-limit-reset' => 'Кількість секунд, яких залишилось чекати до отримання максимальної кількості дозволених запитів'
];

$parameters = [
        'uuid' => 'Ідентифікатор документу. Наприклад: отримати інформацію по списку документів з конкретними uuid <code>filter[uuid][in][]=uuid_1[uuid][in][]=uuid_2 (uuid in (uuid_1, uuid_2))</code>',
    'koatuu' => 'КОАТУУ. Наприклад: отримати документацію по АТО з КОАТУУ 0524587903, <code>filter[koatuu]=0524587903</code>, або документацію по списку АТО',
    'updated_at' => 'Дата оновлення документу. Наприклад: отримати всі документи які були оновлені після дати 2018-09-23, <code>filter[updated_at][>]=2018-09-23</code>',
    'document_type' => 'Тип документу. Наприклад: отримати генеральні плани <code>filter[document_type][like]=генеральний план</code>',
    'decision_number' => 'Номер рішення. Наприклад: Отримати документи, в яких номер рішення про затвердження рівний 1234'
];

$examples = [
        $baseApiUrl.'&page=2' => 'Отримати сторінку номер 2',
        $baseApiUrl.'&filter[koatuu]=8000000000' => 'Отримати всі документи по місті Києві',
    $baseApiUrl.'&filter[ato_name]=Коростишівський Район' => 'Отримати містобудівну документацію по Коростишівському районі',
        $baseApiUrl.'&filter[and][][date_approval][>]=1995-01-01&filter[and][][date_approval][<]=2000-01-01&filter[and][][document_type]=Генеральний план' => 'Отримати всі генеральні плани з 1995-01-01 по 2000-01-01',
    $baseApiUrl.'&filter[koatuu][in][]=5910100000&filter[uuid][in][]=5910500000' => 'Отримати містобудівну документацію по місті Суми та місті Лебедин',
    $baseApiUrl.'&filter[updated_at][>]=2019-07-03' => 'Отримати всі документи, які оновились після 2019-07-03'
];


/**
 * @param $fields
 * @return string
 */
$buildFieldList = function($fields) {

    $html = '<ul>';

    foreach ($fields as $attribute => $label) {
        $html .= '<li><code>'. $attribute .'</code> '. $label .'.</li>';
    }

    $html .= '</ul>';

    return $html;
};

?>

<div class="row api-container">

    <div class="col-md-12 text-center">
        <p class="main-title">Документація API PMAP</p>
    </div>

    <div class="col-xs-12">
        <p>Тестовий ключ доступу <code>access-token</code>: <code><?= $accessToken?></code>. Обмеження кількості запитів для тестового ключа <span class="label label-info">2 запити за секунду</span>.</p>
        <p>Для використання API PMAP напишіть лист в технічну підтримку на цю адресу <?= $techEmail?> для отримання ключа доступу.</p>
    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Приклад запиту</p>
        <p><code>GET <?= $baseApiUrl ?></code></p>
    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Формати відповіді</p>
        <p><code>json, xml</code>. Формат вказується при запиті в HTTP-заголовку <code>Accept: application/json</code> або <code>Accept: application/xml</code></p>
    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Опис полів</p>

        <?= $buildFieldList($fields) ?>

    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Опис заголовків відповіді</p>
        <?= $buildFieldList($headers) ?>
    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Дозволені параметри для пошуку</p>
        <?= $buildFieldList($parameters) ?>
        <p>Ці параметри можна міксувати в запиті. Для більш детальної інформації як будувати запити можна глянути тут <?= \yii\helpers\Html::a('https://www.yiiframework.com/doc/api/2.0/yii-data-datafilter', 'https://www.yiiframework.com/doc/api/2.0/yii-data-datafilter')?> .</p>
        <p>Список параметрів для пошуку може бути розширений. Для цього зверніться в технічну підтримку PMAP за цією адресою <?= $techEmail ?>.</p>
    </div>

    <div class="col-xs-12">
        <p class="sub-title text-center">Приклади</p>
        <?= $buildFieldList($examples) ?>
    </div>

</div>