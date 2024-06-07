<?php
class ArticleTitleService {
    
    const AuthorSeparator = "x";
    const VolumeDefaultDecimals = 2;
    
    private $category;
    private $title;
    private $author;
    private $rawTitle;
    private $volume;
    private $option;
    
    public function __construct($category) {
        if(!$category){
            throw new InvalidArgumentException;
        }
        $this->category = $category;
    }
    
    public function update() {
        $args = func_get_args();
        if(count($args) == 1){
            $this->title = trim($args[0]);
        }
        else if(count($args) == 4){
            $this->author = trim($args[0]);
            $this->rawTitle = trim($args[1]);
            $this->volume = trim($args[2]);
            $this->option = trim($args[3]);
        }
        else{
            throw new InvalidArgumentException;
        }
    }
    
    public function compose() {
        return $this->title = trim(join(" ", array(
            $this->composeAuthor(), $this->composeRawTitle(),
            $this->composeVolume(), $this->composeOption(),
        )));
    }
    
    private function composeAuthor() {
        $author = $this->author;
        if(preg_match("/^\[([^\[\]]+)\]$/", $this->author, $m)){
            $author = trim($m[1]);
        }
        if($author){
            return "[".str_replace(array("×",","), self::AuthorSeparator, $author)."]";
        }
        return "";
    }

    private function composeRawTitle() {
        return $this->rawTitle;
    }

    private function composeVolume() {
        if(!trim($this->volume)) return "";
        $volume = $this->commonConvert(trim($this->volume));
        if(in_array($this->category, array("etc", "doujin"))){
            return $volume;
        }
        if($this->category == "magazine" && preg_match("/^([0-9]{4}(年|-|\/))?([0-9\-, ]+)(月?号)?$/", $volume, $m)){
            $year = $m[1] ? str_replace("/", "年", $m[1]) : date("Y年");
            return $year.$this->formatVolume($m[3]).(isset($m[4]) && $m[4] ? $m[4] : "号");
        }
        if(preg_match("/^(第|全)?([0-9\-, ]+)(巻|集)?$/", $volume, $m)){
            return (isset($m[1]) && $m[1] ? $m[1] : "第").$this->formatVolume($m[2]).(isset($m[3]) && $m[3] ? $m[3] : "巻");
        }
        return $volume;
    }

    private function composeOption() {
        return $this->option;
    }
    
    public function decompose($title = null) {
        if(!$title){
            if($this->title){
                $title = $this->title;
            }
            else{
                throw new InvalidArgumentException;
            }
        }
        $title = $this->commonConvert($title);
        if(preg_match("/^(\(.+?\))? ?\[(.+?)\] ?(.+?) ((第|全)?(([0-9]{1,4}年)?([0-9]{1,4}(-[0-9]{1,4})?)".
                      "( ?, ?[0-9]{1,4}(-[0-9]{1,4})?)*月?)(巻|号|集))? ?(.*?)$/ui", $title, $m)){
            $this->author = trim($m[2]);
            $this->rawTitle = trim($m[3]);
            $this->volume = trim($m[6]);
            $this->option = trim($m[13]);
        }
        else{
            $this->author = "";
            $this->rawTitle = $this->title;
            $this->volume = "";
            $this->option = "";
        }
        return array(
            "author" => $this->author,
            "rawTitle" => $this->rawTitle,
            "volume" => $this->volume,
            "option" => $this->option,
        );
    }
    
    private function formatVolume($volume) {
        $volume = str_replace(" ", "", $volume);
        $elems = preg_split("/[\-,]+/", $volume);
        $seps = Util::removeEmpty(preg_split("/[0-9]+/", $volume));
        $maxDecimal = self::VolumeDefaultDecimals;
        $ret = "";
        foreach($elems as $elem){
            $maxDecimal = max(strlen($elem), $maxDecimal);
        }
        foreach($elems as $i => $elem){
            $ret .= str_pad($elem, $maxDecimal, "0", STR_PAD_LEFT).(isset($seps[$i]) ? $seps[$i] : "");
        }
        return $ret;
    }

    private function commonConvert($raw) {
        return str_replace(
            array("０","１","２","３","４","５","６","７","８","９"), 
            array("0","1","2","3","4","5","6","7","8","9")
        , $raw);
    }
    
}
?>
