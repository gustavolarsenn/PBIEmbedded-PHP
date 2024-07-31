<?php

class PowerBiReportDetails {
    public $reportId;
    public $reportName;
    public $embedUrl;

    public function __construct($reportId, $reportName, $embedUrl) {
        $this->reportId = $reportId;
        $this->reportName = $reportName;
        $this->embedUrl = $embedUrl;
    }
}