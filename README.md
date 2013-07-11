SimplePHPBench v0.5
===================

A simple PHP execution time benchmark

Quick Start
-----------
    
    $PB = new PHPBench(); // output to screen
    $PB = new PHPBench("benchmark.log"); // output to file
    $PB->start();
    $PB->tick("the first tick"); // record this tick
    $PB->tick("the second tick"); // record this tick
    $PB->end();
    $PB->report();
    
    
The output example:

    -------------------------------------------------------------------------------
    >[Seq no.] Timestamp (Elapsed Time) - Memory Usage (Memory Peak) - Description
    -------------------------------------------------------------------------------
    >[1] 1371274724.0709700 (+0.0000000 secs) - 288112 bytes (302960 bytes) - PHPBench starts
    >[2] 1371274724.0712380 (+0.0002680 secs) - 299840 bytes (304360 bytes) - the first tick
    >[3] 1371274724.0713130 (+0.0003430 secs) - 303984 bytes (308504 bytes) - the second tick
    >[4] 1371274724.0715560 (+0.0005860 secs) - 317832 bytes (322496 bytes) - PHPBench ends

    =========================================
    > Elapsed Time: 0.0005860 secs
    > Declared Classes: 4
    > Included Files: 3
    > Peak Used Memory: 322496 bytes
    =========================================

<br />
- - -
###### by _Yen-Chun Hsu_ #######
- - -
