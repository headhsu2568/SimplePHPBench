<?
class PHPBench {
    public $baseline;
    public $startTime;
    public $endTime;
    public $tickTimes;
    public $outfile=null;

    public function __construct($outfile=null) {
        if(!is_null($outfile) && !empty($outfile)) {
            $this->outfile = $outfile;
        }
        $this->tickTimes = array();
        $this->baseline = 0;
    }

    public function setOutfile($outfile=null) {
        if(!is_null($outfile) && !empty($outfile)) {
            $this->outfile = $outfile;
        }
    }

    public function setBaseline($baseline=null) {
        if(!is_null($baseline)) $this->baseline = $baseline-1;
        else $this->baseline = count($this->tickTimes)-1;
    }

    public function start($desc="PHPBench starts") {
        if(count($this->tickTimes) > 0) echo "[Error] PHPBench is alreay started\n";
        else {
            $this->startTime = array(microtime(true), $desc);
            array_push($this->tickTimes, $this->startTime);
        }
    }

    public function restart($desc="PHPBench restarts") {
        $this->tickTimes = array();
        $this->baseline = 0;
        $this->start($desc);
    }

    public function tick($desc="", $baseline=false) {
        if(count($this->tickTimes) < 1) echo "[Error] PHPBench is not started\n";
        else {
            array_push($this->tickTimes, array(microtime(true), $desc));
            if($baseline === true) $this->baseline = count($tickTimes)-1;
        }
    }

    public function end($desc="PHPBench ends") {
        if(count($this->tickTimes) < 1) echo "[Error] PHPBench is not started\n";
        else {
            $this->endTime = array(microtime(true), $desc);
            array_push($this->tickTimes, $this->endTime);
        }
    }

    public function report($html=false, $showFormat=true) {
        if(!is_null($this->outfile)) ob_start();
        if($html === true) $br = "<br/>";
        else $br = "\n";
        $base = $this->tickTimes[$this->baseline][0];
        if($showFormat === true) {
            echo "---------------------------------------------------".$br;
            echo "[No.] Micro Seconds (Elapsed Time) - Description".$br;
            echo "---------------------------------------------------".$br;
        }
        foreach($this->tickTimes as $i => $tick) {
            $offset = $tick[0] - $base;
            if($offset >= 0) $offset = "+".$offset;
            echo "[".($i+1)."] ".$tick[0]."(".$offset.") - ".$tick[1].$br;
        }
        if(!is_null($this->outfile)) {
            $w = ob_get_contents();
            $fp = fopen($this->outfile, "a");
            fwrite($fp, $w);
            fclose($fp);
            ob_end_clean();
        }
    }
}
?>
