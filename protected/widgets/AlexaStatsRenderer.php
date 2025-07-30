<?php
class AlexaStatsRenderer extends Widget
{
    /**
     * @var WebdataAlexa $alexa
     */
    public $alexa;

    public function run() {
        $version = Helper::_v($this->alexa, "version", "0");
        $renderer = "renderVersion".$version;
        $tmpl = "alexa/alexa_{$version}";
        return $this->$renderer($tmpl);
    }

    private function renderVersion0($tmpl)
    {
        return $this->render($tmpl, array(
            "alexa"=>$this->alexa
        ));
    }

    private function renderVersion4_0_5($tmpl)
    {
        $similar_sites = array();
        $related_keywords = array();
        $delta_direction = false;
        $delta = 0;

        if($data = @json_decode($this->alexa['data'], true)) {
            $similar_sites = Helper::_v($data, 'similar_sites', array());
            $related_keywords = Helper::_v($data, 'related_keywords', array());
            $delta_direction = Helper::_v($data, 'delta_direction', false);
            $delta = Helper::_v($data, 'delta', 0);
        }

        return $this->render($tmpl, array(
            "alexa"=>$this->alexa,
            "similar_sites"=>$similar_sites,
            "related_keywords"=>$related_keywords,
            "delta_direction"=>$delta_direction,
            "delta"=>$delta,
        ));
    }
}