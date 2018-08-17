<?php

namespace Longbang\Llaravel\Controllers;

use Longbang\Llaravel\Controllers\CommonController;
use Longbang\Llaravel\Classes\LanguageClasses;
use Longbang\Llaravel\Api\CCLanguages as CCSHOP;

class LanguageController extends CommonController
{
    public $language_m = null;//语言与货币对象
    private $CCSHOP = null;

    public function __construct()
    {
        $this->CCSHOP = new CCSHOP();
        $this->language_m = new LanguageClasses();
    }

    public static function setLanguage($match_lang=null,$type='language')
    {
        static::newSelf();
        self::$SELF->CCSHOP->isApi();
        switch ($type){
            case 'language':$match = self::$SELF->language_m->match_language($match_lang);
                break;
            case 'currency':$match = self::$SELF->language_m->match_currency($match_lang);
                break;
            case 'ip':$match = self::$SELF->language_m->match_ip($match_lang);/*用ip匹配到的语言*/
                break;
            case 'browser':$match = self::$SELF->language_m->match_browser($match_lang);/*用浏览器匹配到的语言*/
                break;
            default:$match = self::$SELF->language_m->match_language('en-us');/*默认语言*/
        }
        return $match->setLanguage();
    }

    /*获取语言管理里面的所有数据*/
    public static function getLanguageAll()
    {
        static::newSelf();
        if(empty(self::$SELF->CCSHOP->isApi('error'))){
            $language_all_arr = self::$SELF->language_m->divideCountryLanguageCurrency;
        }
        if(empty($language_all_arr)){
            $language = new \Jason\Ccshop\Models\Language();
            $language_all = $language->orderBy('sort', 'ASC')->get()->toArray();
            $arr=[];
            foreach($language_all as $k=>$v){
                $arr[$k] = array('id'=>$v['id'],'name'=>$v['name'],'is_enabled'=>$v['is_enabled'],'is_default'=>$v['is_default'],'sort'=>$v['sort'],'language'=>null,'country'=>null,'currency'=>null);
                $data = explode('~',$v['code']);
                if(empty($data[0])||empty($data[1])){
                    break;
                }
                $language = stripos($data[0],'-');
                if($language > -1){
                    $arr[$k]['language'] = substr($data[0],0,$language);
                    $arr[$k]['country'] = ltrim(substr($data[0],$language),'-');
                    if(stripos($data[0],'zh') > -1){
                        $arr[$k]['language'] = 'ZH-CN';
                    }
                }else{
                    $arr[$k]['language'] = $data[0];
                    $arr[$k]['country'] = $data[0];
                }
                $arr[$k]['currency'] = $data[1];
            }
            $language_all_arr = $arr;
        }
        return $language_all_arr;
    }
}