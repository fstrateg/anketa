<?php
namespace common\components;

use common\models\User;
use common\models\SettingsRecord;
use yii\base\Component;
use yii\base\ErrorException;

class Telegram extends Component
{
    public $class = '';
    public $token = '';
    public $url = '';

    function __construct(array $config=[])
    {
        $config=SettingsRecord::getValuesGroup('telegram');
        parent::__construct($config);
    }

    /**
     * @param $chat_id
     * @param $msg
     */
    public function sendMessage($userName, $msg, $forinfo='')
    {
        $user=User::findOne(['username'=>$userName]);
        if (!$user&&$user->telegram) return;
        $this->sendMessageInChat($user->telegram,$msg,$forinfo);
    }

    public function sendMessageAll($msg,$forinfo='')
    {
        $users=User::find()->where('not telegram is null')->all();
        if (!$users) return;
        foreach($users as $u)
        {
            $this->sendMessageInChat($u->telegram,$msg,$forinfo);
        }
    }

    public function sendMessageInChat($chat_id,$msg,$forinfo='')
    {
        $data=[];
        $data['parse_mode']='html';
        $data['chat_id']=$chat_id;
        $data['text'] = $forinfo?"<b>{$forinfo}</b>\r\n".$msg:$msg;


        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);

        try {
            $result = file_get_contents("{$this->url}bot{$this->token}/sendMessage", false, $context);
        }catch(ErrorException $err){}
        //print_r($result);
        //$query = "{$this->apiUrl}{$this->token}/{$api_method}";
    }

    /**
     * @return Telegram
     */
    public static function instance()
    {
        return new Telegram();
    }
}