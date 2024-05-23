<?php

class EmbedConfig {
    public $type;
    public $reportsDetail;
    public $embedToken;

    public function __construct($type = null, $reportsDetail = null, $embedToken = null) {
        $this->type = $type;
        $this->reportsDetail = $reportsDetail;
        $this->embedToken = $embedToken;
    }
}
