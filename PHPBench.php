<?
class PHPBench {
    public $quiet=false;
    public $baseline;
    public $current;
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
        $this->current = 0;
    }

    public function setQuiet() {
        $this->quiet = true;
    }

    public function setOutfile($outfile=null) {
        if(!is_null($outfile) && !empty($outfile)) {
            $this->outfile = $outfile;
        }
    }

    public function setBaseline($baseline=null) {
        if(!is_null($baseline) && is_numeric($baseline)) $this->baseline = $baseline-1;
        else $this->baseline = $this->current-1;
    }

    public function start($desc="PHPBench starts") {
        if($this->current != 0) {
            if($this->quiet === false) echo "[Error] PHPBench is alreay started\n";
        }
        else {
            ++$this->current;
            $this->startTime = array(">[".$this->current."]" ,microtime(true), $desc);
            array_push($this->tickTimes, $this->startTime);
        }
    }

    public function restart($desc="PHPBench restarts") {
        $this->tickTimes = array();
        $this->baseline = 0;
        $this->current = 0;
        $this->start($desc);
    }

    public function tick($desc="", $baseline=false) {
        if($this->current < 1) {
            if($this->quiet === false) echo "[Error] PHPBench is not started yet\n";
        }
        else {
            ++$this->current;
            array_push($this->tickTimes, array(">[".$this->current."]", microtime(true), $desc));
            if($baseline === true) $this->baseline = $this->current-1;
        }
    }

    public function end($desc="PHPBench ends") {
        if($this->current < 1) {
            if($this->quiet === false) echo "[Error] PHPBench is not started yet\n";
        }
        else {
            ++$this->current;
            $this->endTime = array(">[".$this->current."]", microtime(true), $desc);
            array_push($this->tickTimes, $this->endTime);
        }
    }

    public function report($html=false, $showFormat=true) {
        if(!is_null($this->outfile)) ob_start();
        if($html === true) $br = "<br/>";
        else $br = "\n";
        $base = $this->tickTimes[$this->baseline][1];
        if($showFormat === true) {
            echo "---------------------------------------------------".$br;
            echo ">[Sequence no.] Micro Seconds (Elapsed Time) - Description".$br;
            echo "---------------------------------------------------".$br;
        }
        foreach($this->tickTimes as $i => $tick) {
            $offset = $tick[1] - $base;
            if($offset >= 0) $offset = "+".$offset;
            echo $tick[0]." ".$tick[1]."(".$offset.") - ".$tick[2].$br;
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
