<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午4:20
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Entity;

use App\Http\Controllers\smk_systems\YuCtrl;

class FileEntity
{
    private $media_id;
    private $path;
    private $corp_id;

    /**
     * @return mixed
     */
    public function getCorpId()
    {
        return $this->corp_id;
    }

    /**
     * @param mixed $corp_id
     */
    public function setCorpId($corp_id)
    {
        $this->corp_id = $corp_id;
    }

    /**
     * @return mixed
     */
    public function getMediaId($type,$storage=false)
    {
        //dump($storage);
        $y = new YuCtrl();
        $data = $y->upload_file_to_wx($type,$this->path,$this->getCorpId(),$storage);
        if(isset($data->errcode)&&$data->errcode==0){
            $m_id = $data->media_id;
            $p = array('media_id'=>$m_id);
            return $p;
        }
    }

    /**
     * @param mixed $media_id
     */
    public function setMediaId($media_id)
    {
        $this->media_id = $media_id;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


}