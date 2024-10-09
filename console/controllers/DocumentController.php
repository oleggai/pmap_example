<?php

use common\components\AnalyticsComponent;
use common\components\DocumentComponent;
use common\models\ato\Ato;
use common\models\document\AnotherDoc;
use common\models\document\Document;
use common\models\document\MasterPlan;
use common\models\document\ZoningPlan;
use common\models\NotWorkedLink;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 *
 */
class DocumentController extends Controller
{
    /**
     *
     */
    public function actionSendEmailAboutNotWorkedLinks()
    {
        /* @var $documentComponent DocumentComponent */
        $documentComponent = \Yii::$app->documentComponent;

        $documentComponent->sendEmailAboutNotWorkedLinks();
    }

    /**
     *
     */
    public function actionDetectNotWorkedLinks()
    {
        /* @var $documentComponent DocumentComponent */
        $documentComponent = \Yii::$app->documentComponent;

        NotWorkedLink::deleteAll();

        /* @var $documentComponent DocumentComponent */
        $query = Document::find()
            ->innerJoinWith(['ato'])
            ->where(['not', ['ato.latitude' => null]])
            ->andWhere('((link_decision is not null and link_decision <> "") || (link_text is not NULL and link_text <> "") || (link_graphic is not NULL and link_graphic <> ""))')
            ->andWhere(['not', ['ato.type_ato' => Ato::$villageCouncil]]);

        /* @var $document Document */
        foreach ($query->each(1000) as $document) {
            $linkNames = ['link_decision', 'link_text', 'link_graphic'];

            foreach ($linkNames as $linkName) {
                if($document->{$linkName}) {

/*                    $this->stdout('Document id: '.$document->id.PHP_EOL);
                    $this->stdout($document->{$linkName}.PHP_EOL);*/

                    if(!$documentComponent->isAvailable($document->{$linkName})) {
                        $notWorkedLink = new NotWorkedLink();
                        $notWorkedLink->document_id = $document->id;
                        $notWorkedLink->link = $document->{$linkName};
                        $notWorkedLink->date_check = (new \DateTime())->format('Y-m-d H:i:s');
                        $notWorkedLink->save();
                    }
                }
            }
        }

        $documentComponent->sendEmailAboutNotWorkedLinks();
    }

    /**
     *
     */
    public function actionFixDocuments()
    {
        $documents = Document::find()->where(['YEAR(date_approval)' => 1111])->all();

        /* @var $document Document */
        foreach ($documents as $document) {
            $document->id_authority = null;
            $document->name = null;
            $document->date_approval = null;
            $document->decision_number = null;
            $document->link_decision = null;
            $document->link_text = null;
            $document->link_graphic = null;
            $document->zoning_plan = null;
            $document->percentage_coverage = null;
            $document->no_doc = null;
            $document->info_territory = null;
            $document->date_actualization = null;
            $document->name_actualization = null;
            $document->decision_number_actualization = null;

            $document->updateAttributes([
                'id_authority', 'name', 'date_approval', 'decision_number', 'link_decision', 'link_text',
                'link_graphic', 'zoning_plan', 'percentage_coverage', 'no_doc', 'info_territory', 'date_actualization',
                'name_actualization', 'decision_number_actualization'
            ]);
        }

        $documents = Document::find()->where(['like', 'link_decision', '%--', false])->all();
        /* @var $document Document */
        foreach ($documents as $document) {
            $document->link_decision = null;
            $document->updateAttributes(['link_decision']);
        }


        $documents = Document::find()->where(['like', 'link_text', '%--', false])->all();
        /* @var $document Document */
        foreach ($documents as $document) {
            $document->link_text = null;
            $document->updateAttributes(['link_text']);
        }

        $documents = Document::find()->where(['like', 'link_graphic', '%--', false])->all();
        /* @var $document Document */
        foreach ($documents as $document) {
            $document->link_graphic = null;
            $document->updateAttributes(['link_graphic']);
        }
    }

    /**
     *
     */
    protected function actionDeleteDocuments()
    {
        /* @var $region Ato Закарпатська область */
        $region = Ato::find()->where(['koatuu' => '2100000000'])->one();

        /* @var $children Ato[]*/
        $children = $region
            ->children()
            ->with(['documents'])
            ->andWhere([
                'type_ato' => Ato::$villageData])
            ->all();

        foreach ($children as $village) {

            Document::deleteAll(['id' => ArrayHelper::getColumn($village->documents, 'id')]);

            $village->updateStateDocumentation();
        }
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteDuplicates()
    {
        /* @var $analyticsComponent AnalyticsComponent */
        $analyticsComponent = \Yii::$app->analyticsComponent;

        $analyticsComponent->deleteDuplicates();
    }

    /**
     *
     */
    public function actionInitStateDocumentation()
    {
        try {
            /* @var $ato Ato */
            foreach (Ato::find()->with('documents')->each(200) as $ato) {

                if(!$ato->getDocuments()->count()) {
                    $ato->state_documentation = Ato::STATE_DOCUMENTATION_NOT_FILLED;
                    $ato->save(false);
                    continue;
                }

                /* @var $document Document */
                foreach ($ato->documents as $document) {
                    if($document->getNo_info()) {
                        $ato->state_documentation = Ato::STATE_DOCUMENTATION_PART_FILLED;
                        $ato->save(false);
                        continue 2;
                    }
                }

                $ato->state_documentation = Ato::STATE_DOCUMENTATION_FILLED;
                $ato->save(false);

            }
            $this->stdout('Command executed successfully', Console::FG_GREEN);
        } catch(\Exception $e) {
            $this->stdout('Some error occurred: '.$e->getMessage(), Console::FG_RED);
        }
    }

    /**
     * @deprecated
     */
    private function actionMoveLinks()
    {
        try {
            $documents = Document::find();
            /* @var $document Document */
            foreach ($documents->each(100) as $document) {
                $document->link_decision = $document->link_text;
                $document->link_text = $document->link_graphic;
                $document->save(false);
            }
            $this->stdout('Command executed successfully', Console::FG_GREEN);
        } catch (\Exception $e) {
            $this->stdout('Error occurred: '.$e->getMessage());
        }
    }
}