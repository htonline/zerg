<?php


namespace app\api\controller\v1;




use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,...（传参形式）
     * @return 一组theme模型（数组）
     */
    public function getSimpleList($ids=''){
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        $result = ThemeModel::getThemeByID($ids);
        if($result->isEmpty()){
            throw new ThemeException();
        }
        return $result;
    }

    //
    /**
     * @url /theme/:id
     * 只接收一个id参数，是因为只点击了某一个专题
     */
    public function getComplexOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if(!$theme){
            throw new ThemeException();
        }
        return $theme;

    }


}