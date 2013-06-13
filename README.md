SimplePHPBench v0.2
===================

A simple PHP execution time benchmark

Quick Start
-----------
    
    $PB = new PHPBench(); // output to screen
    $PB = new PHPBench("result.log"); // output to screen
    $PB->start();
    $PB->tick("the first tick"); // record this tick
    $PB->end();
    $PB->report();
    
    
The output example:

    ---------------------------------------------------
    [No.] Micro Seconds (Elapsed Time) - Description
    ---------------------------------------------------
    [1] 1370857426.059(0) - PHPBench starts
    [2] 1370857427.0591(+1.0001420974731) - the first tick
    [3] 1370857428.0593(+2.0003011226654) - PHPBench ends

<br />
- - -
###### by _Yen-Chun Hsu_ #######
- - -
