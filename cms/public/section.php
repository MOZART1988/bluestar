<?
include_once(_PUBLIC_ABS_."/objects.php");

class Section extends objects {

    public $sectionId;
    public $sectionName;
    public $sectionTitle;
    public $headMenuId;
    public $classesListId;
    public $mainSliderId;
    public $imageMenuId;
    public $minMenuId;
    public $modelsHeadId;
    public $bodyTypeListId;

    private $data;

    public $mainSectionLink;

    const sectionClassId = 30;
    const headMenuClassId = 31;
    const modelClassId = 32;
    const classesClassId = 55;
    const bodyTypeClassId = 54;
    const amgModelsHeadId = 624;
    const mainSliderClassId = 33;
    # ------ меню под главным слайдером с рисунками --------
    const imageMenuClassId = 36;
    # ------ ID маленького меню под главным слайдером -------
    const minMenuClassId = 37;


    function __construct($lang) {

        $this->objects = new objects( $lang );

        $sections = $this->objects->getFullObjectsListByClass(0, self::sectionClassId);
        if (!empty($_GET['section']) && $keyId = $this->recursive_array_search($_GET['section'], $sections)){
            $this->data = $sections[$keyId];
        }else{
            $this->data = $sections[0];
        }


        $this->sectionId = $this->data['id'];
        $this->sectionName = $this->data['Ссылка'];
        $this->sectionTitle = $this->data['Заголовок'];

        $sectionHeadMenu = $this->objects->getObjectsListByClass($this->sectionId, self::headMenuClassId, $sql="AND o.active='1' ORDER BY o.sort LIMIT 1");
        $this->headMenuId = !empty($sectionHeadMenu) ? $sectionHeadMenu['id'] : 0;

        $this->modelsHeadId = 0;

        $modelsList = $this->objects->getObjectsListByClass($this->sectionId, self::modelClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($modelsList)){
            $this->modelsHeadId = $modelsList['id'];
        }

        $classesList = $this->objects->getObjectsListByClass($this->sectionId, self::classesClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($classesList)){
            $this->classesListId = $classesList['id'];
        }

        $bodyTypeList = $this->objects->getObjectsListByClass($this->sectionId, self::bodyTypeClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($bodyTypeList)){
            $this->bodyTypeListId = $bodyTypeList['id'];

        }

        $mainSliderList = $this->objects->getObjectsListByClass($this->sectionId, self::mainSliderClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($mainSliderList)){
            $this->mainSliderId = $mainSliderList['id'];
        }

        $imageMenuList = $this->objects->getObjectsListByClass($this->sectionId, self::imageMenuClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($imageMenuList)){
            $this->imageMenuId = $imageMenuList['id'];
        }

        $minMenuList = $this->objects->getObjectsListByClass($this->sectionId, self::minMenuClassId , $sql = "AND o.active='1' ORDER BY o.sort LIMIT 1");
        if(count($minMenuList)){
            $this->minMenuId = $minMenuList['id'];
        }


//        if(count($sectionData)){
//            foreach($sectionData as $key => $value)
//            {
//                if(!empty($value)){
//                    $this->{$key} = $value;
//                }
//            }
//        }
//        if (@$_GET['sectionId'] && is_numeric($_GET['sectionId'])) {
//            $this->sectionId = $_GET['sectionId'];
//            $o = $this->objects->getObjectsListByClass($this->sectionId, 31);
//            var_dump($o);die();
//        }



        $this->mainSectionLink = '<a href="/'.$_GET['lang'].'/'.$this->sectionName.'/">'.$this->sectionTitle.'</a>';

    }

    private function recursive_array_search($needle,$haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}

?>