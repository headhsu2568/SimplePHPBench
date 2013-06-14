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

    public function getTime() {

        /*** 
         * refer to http://se2.php.net/manual/en/function.microtime.php#101875
         * thanks https://github.com/victorjonsson/PHP-Benchmark/blob/master/lib/PHPBenchmark/Monitor.php
         ***/
        list($u, $s) = explode(" ", microtime(false));
        if(function_exists("bcadd")) $t = bcadd($u, $s, 7);
        else $this->bcadd($u, $s, 7);
        return $t;
    }

    public function bcadd($left_operand, $right_operand, $scale) {
        @list($ll, $lr) = @explode(".", $left_operand);
        if(is_null($ll)) $ll = "0";
        if(is_null($lr)) $lr = "0";
        $lrlen = strlen($lr);
        @list($rl, $rr) = @explode(".", $right_operand);
        if(is_null($rl)) $rl = "0";
        if(is_null($rr)) $rr = "0";
        $rrlen = strlen($rr);
 
        /*** right alignment (padding zero) for right numbers ***/
        if($lrlen > $rrlen) $rr = str_pad($rr, $lrlen, "0", STR_PAD_RIGHT);
        else if($rrlen > $lrlen) $lr = str_pad($lr, $rrlen, "0", STR_PAD_RIGHT);
 
        /*** caculate the amount of zero of right prefix ***/
        $lrzlen = strlen($lr) - strlen(intval($lr));
        $rrzlen = strlen($rr) - strlen(intval($rr));
        $zerolen = ($lrzlen < $rrzlen) ? $lrzlen : $rrzlen;
 
        $left = $ll + $rl;
        $right = $lr + $rr;
 
        /*** check whether the result of right numbers is carried ***/
        if((strlen($right) > strlen(intval($lr))) && (strlen($right) > strlen(intval($rr)))) {
            if($zerolen > 0) --$zerolen;
            else {
                $left = $left + substr($right, 0, 1);
                $right = substr($right, 1);
            }
        }
 
        /*** preserve the scale number digit of the result of right numbers ***/
        if(strlen($right) > ($scale - $zerolen)) $right = round($right, $scale-$zerolen-strlen($right));
        else $right = str_pad($right, $scale-$zerolen, "0", STR_PAD_RIGHT);
 
        /*** left alignment (padding zero) for the result of right numbers ***/
        if($zerolen > 0) $right = str_pad($right, $scale, "0" ,STR_PAD_LEFT);
 
        return $left.".".$right;
    }

    public function start($desc="PHPBench starts") {
        if($this->current != 0) {
            if($this->quiet === false) echo "[Error] PHPBench is alreay started\n";
        }
        else {
            ++$this->current;
            $this->startTime = array(">[".$this->current."]", $this->getTime(), $desc);
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
            array_push($this->tickTimes, array(">[".$this->current."]", $this->getTime(), $desc));
            if($baseline === true) $this->baseline = $this->current-1;
        }
    }

    public function end($desc="PHPBench ends") {
        if($this->current < 1) {
            if($this->quiet === false) echo "[Error] PHPBench is not started yet\n";
        }
        else {
            ++$this->current;
            $this->endTime = array(">[".$this->current."]", $this->getTime(), $desc);
            array_push($this->tickTimes, $this->endTime);
        }
    }

    public function report($html=false, $showFormat=true) {
        if(!is_null($this->outfile)) ob_start();
        if($html === true) $br = "<br/>";
        else $br = "\n";
        $base = $this->tickTimes[$this->baseline][1];
        if($showFormat === true) {
            echo "-----------------------------------------------------------".$br;
            echo ">[Sequence no.] Micro Seconds (Elapsed Time) - Description".$br;
            echo "-----------------------------------------------------------".$br;
        }
        foreach($this->tickTimes as $i => $tick) {
            if(function_exists("bcsub")) $offset = bcsub($tick[1], $base, 7);
            else $offset = $this->bcsub($tick[1], $base, 7);
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
