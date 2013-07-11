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
        else $t = $this->bcadd($u, $s, 7);
        return $t;
    }

    public function getMem() {
        $mem = memory_get_usage();
        $peak = memory_get_peak_usage();
        return array($mem, $peak);
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
        $origrlen = strlen($lr);
 
        $left = $ll + $rl;
        $right = $lr + $rr;
 
        /*** check whether the result of right numbers is carried ***/
        if(strlen($right) > $origrlen) {
            $len = strlen($right)-$origrlen;
            $left = $left + substr($right, 0, $len);
            $right = intval(substr($right, $len));
        }
        $zerolen = $origrlen - strlen($right);
 
        /*** preserve the scale number digit of the result of right numbers ***/
        if(strlen($right) > ($scale - $zerolen)) $right = round($right, $scale-$zerolen-strlen($right));
        else $right = str_pad($right, $scale-$zerolen, "0", STR_PAD_RIGHT);
 
        /*** left alignment (padding zero) for the result of right numbers ***/
        if($zerolen > 0) $right = str_pad($right, $scale, "0" ,STR_PAD_LEFT);
 
        return $left.".".$right;
    }

    public function bcsub($left_operand, $right_operand, $scale) {
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
        $origrlen = strlen($lr);
 
        /*** check whether the result is negtive ***/
        $negtive = false;
        $ill = intval($ll);
        $irl = intval($rl);
        if($ill < $irl) $negtive = true;
        else if($ill === $irl) {
            $ilr = intval($lr);
            $irr = intval($rr);
            if($ilr < $irr) $negtive = true;
            else if($irl === $irr) return "0".str_pad("", $scale, "0");
        }
 
        /*** if the result is negtive, swap the left and the right ***/
        if($negtive === true) {
            $tmp = $ll;
            $ll = $rl;
            $rl = $tmp;
            $tmp = $lr;
            $lr = $rr;
            $rr = $tmp;
        }
 
        if(intval($lr) < intval($rr)) {
            $lr = "1".$lr;
            --$ll;
        }
        $left = $ll - $rl;
        $right = $lr - $rr;
        $zerolen = $origrlen - strlen($right);
 
        /*** preserve the scale number digit of the result of right numbers ***/
        if(strlen($right) > ($scale - $zerolen)) $right = round($right, $scale-$zerolen-strlen($right));
        else $right = str_pad($right, $scale-$zerolen, "0", STR_PAD_RIGHT);
 
        /*** left alignment (padding zero) for the result of right numbers ***/
        if($zerolen > 0) $right = str_pad($right, $scale, "0" ,STR_PAD_LEFT);
 
        if($negtive === true) return "-".$left.".".$right;
        else return $left.".".$right;
    }

    public function start($desc="PHPBench starts") {
        if($this->current != 0) {
            if($this->quiet === false) echo "[Error] PHPBench is already started\n";
        }
        else {
            ++$this->current;
            $this->startTime = array(">[".$this->current."]", $this->getTime(), $this->getMem(), $desc);
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
            array_push($this->tickTimes, array(">[".$this->current."]", $this->getTime(), $this->getMem(), $desc));
            if($baseline === true) $this->baseline = $this->current-1;
        }
    }

    public function end($desc="PHPBench ends") {
        if($this->current < 1) {
            if($this->quiet === false) echo "[Error] PHPBench is not started yet\n";
        }
        else {
            ++$this->current;
            $this->endTime = array(">[".$this->current."]", $this->getTime(), $this->getMem(), $desc);
            array_push($this->tickTimes, $this->endTime);
        }
    }

    public function report($html=false, $showFormat=true) {
        if(!is_null($this->outfile)) ob_start();
        if($html === true) $br = "<br/>";
        else $br = "\n";
        $base = $this->tickTimes[$this->baseline][1];
        if($showFormat === true) {
            echo "-------------------------------------------------------------------------------".$br;
            echo ">[Seq no.] Timestamp (Elapsed Time) - Memory Usage (Memory Peak) - Description".$br;
            echo "-------------------------------------------------------------------------------".$br;
        }
        foreach($this->tickTimes as $i => $tick) {
            if(function_exists("bcsub")) $offset = bcsub($tick[1], $base, 7);
            else $offset = $this->bcsub($tick[1], $base, 7);
            if($offset >= 0) $offset = "+".$offset;
            echo $tick[0]." ".$tick[1]." (".$offset." secs) - ".$tick[2][0]." bytes (".$tick[2][1]." bytes) - ".$tick[3].$br;
        }
        if(function_exists("bcsub")) $elapsedTime = bcsub($tick[1], $this->tickTimes[0][1], 7);
        else $elapsedTime = $this->bcsub($tick[1], $this->tickTimes[0][1], 7);
        echo $br;
        echo "=========================================".$br;
        echo "> Elapsed Time: ".$elapsedTime." secs".$br;
        echo "> Declared Classes: ".count(get_declared_classes()).$br;
        echo "> Included Files: ".count(get_included_files()).$br;
        echo "> Peak Used Memory: ".$tick[2][1]." bytes".$br;
        echo "=========================================".$br;
        echo $br;
        $this->reportExtend($br);
        if(!is_null($this->outfile)) {
            $w = ob_get_contents();
            $fp = fopen($this->outfile, "a");
            fwrite($fp, $w);
            fclose($fp);
            ob_end_clean();
        }
    }

    public function reportExtend($br) {}
}
?>
