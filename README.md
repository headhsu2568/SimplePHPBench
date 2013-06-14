SimplePHPBench v0.3
===================

A simple PHP execution time benchmark

Quick Start
-----------
    
    $PB = new PHPBench(); // output to screen
    $PB = new PHPBench("result.log"); // output to screen
    $PB->start();
    $PB->tick("the first tick"); // record this tick
    $PB->tick("the second tick"); // record this tick
    $PB->end();
    $PB->report();
    
    
The output example:

    -----------------------------------------------------------
    >[Sequence no.] Micro Seconds (Elapsed Time) - Description
    -----------------------------------------------------------
    >[1] 1371202815.2883010(+0.0000000) - PHPBench starts
    >[4] 1371202815.2884330(+0.0001320) - the first tick
    >[5] 1371202815.2884570(+0.0001560) - the second tick
    >[6] 1371202815.2884870(+0.0001860) - PHPBench ends

<br />
- - -
###### by _Yen-Chun Hsu_ #######
- - -
