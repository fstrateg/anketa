<?php
namespace frontend\controllers;

use common\components\Telegram;
use frontend\models\AnketadetRecord;
use frontend\models\AnketaModel;
use frontend\models\AnketaForm;
use frontend\models\AnketaRecord;
use frontend\models\RezultModel;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;
use Dompdf\Dompdf;
use yii\base\Security;
//use \yii2tech\html2pdf\Manager;
//use mpdf\mpdf\Mpdf;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['anketa']);
        return $this->render('index');
    }

    public function actionAnketa()
    {
        $session=Yii::$app->session;
        if (!$session->isActive) $session->open();
        $req=AnketaRecord::find()->where(['session'=>$session->getId()])->one();
        if (!$req)
        {
            $model = new AnketaForm();
            if (Yii::$app->request->isPost)
            {
                    $model->load(Yii::$app->request->post());
                    $model->videoFile = UploadedFile::getInstance($model, 'videoFile');
                    if ($model->videoFile && $model->validate())
                    {
                        $model->uploadfile();
                        $model->saveToDb();
                        return $this->redirect(['profile']);
                    }
            }
            return $this->firstPage($model);
        }
        $rws=AnketadetRecord::find()->where(['anketaid'=>$req->id])->count();
        if ($rws) return $this->redirect('done');
        return $this->redirect(['profile']);
    }

    public function actionProfile()
    {
        $session=Yii::$app->session;
        if (!$session->isActive) $session->open();
        $req=AnketaRecord::find()->where(['session'=>$session->getId()])->one();
        if (!$req)
        {
            return $this->redirect('anketa');
        }
        $anketa=new AnketaModel();
        $model=$anketa->createFormModel();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate())
            {
                $anketa->save($req->id);
                $this->sendMessage($req);
                $this->redirect('done');
            }
        }
        return $this->render('anketa',['model'=>$anketa,'fio'=>$req->fio]);
    }

    private function sendMessage($req)
    {
        $t=new Telegram();
        $msg="Добавлена новая анкета от ".$req->fio."\n";
        $msg=$msg." Результат можно посмотреть по адресу:\n".Url::base(true)."/site/rezult?id=".$req->id;
        $t->sendMessageAll($msg,"Добавлена новая анкета");
    }

    public function actionDone()
    {
        return $this->render('done');
    }
    private function firstPage($model)
    {
        return $this->render('video',['model'=>$model]);
    }

    public function actionRezult($id=-1)
    {
        $model=new RezultModel();
        $model->initRezult($id);
        $html=$this->renderAjax('htmlrez',['model'=>$model]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        return $dompdf->stream('rezult',['Attachment'=>false]);



        return $html;
    }

    public function actionTest()
    {
        $t=new Telegram();
        $session=Yii::$app->session;
        if (!$session->isActive) $session->open();
        $req=AnketaRecord::find()->where(['session'=>$session->getId()])->one();
        $msg="Добавлена новая анкета от ".$req->fio."\n";
        $msg=$msg." Результат можно посмотреть по адресу:\n".Url::base(true)."/site/rezult?id=".$req->id;
        $t->sendMessageAll($msg,"Добавлена новая анкета");
    }
}
