# SSOCR-WS - Seven Segment Optical Character Recognition - Web Service

[![Project Stage Badge: Experimental]][Project Stage Page]
[![Codacy Badge]][Codacy Page]
[![License Badge]][GPL3+]
<!-- 
@TODO: 
[![Build Status Badge]][Project Codeship Page]
[![Version Badge]][Releases Page]
-->

[SSOCR] (which stands for Seven Segment Optical Character Recognition) was created 
by [Erik Auerswald] under a [GPL3+] License to recognize digits of a [seven 
segment display]. An image of one row of digits is used for input and the 
recognized number is written to the standard output.

This repository offers the functionality that program as a web service.

## Requirements

This project uses the Silex framework, the Symfony Process component and the 
Composer autoloader. These can be installed by running `composer install` from
the project root. More information on this can be found in the [Composer manual 
"basic usage" section].

Obviously `ssocr` is also needed. For Linux systems that can be installed with 
the following commands (use your personal flavours at your own descretion):

    sudo apt-get install libimlib2-dev
    wget --progress=bar 'http://www.unix-ag.uni-kl.de/~auerswal/ssocr/ssocr-2.16.0.tar.bz2'
    tar -xjvf 'ssocr-2.16.0.tar.bz2'
    cd 'ssocr-2.16.0'
    make
    make install


<!-- Live version running on heroku: http://ssocr.herokuapp.com/ -->

[Composer manual "basic usage" section]: https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies
[SSOCR]: http://www.unix-ag.uni-kl.de/~auerswal/ssocr/
[seven segment display]: https://en.wikipedia.org/wiki/Seven-segment_display
[Erik Auerswald]: https://github.com/auerswal/

[Project Stage Page]: http://bl.ocks.org/potherca/a2ae67caa3863a299ba0
[Releases Page]: /releases/
[Codacy Page]: https://www.codacy.com/public/potherca/ssocr-ws.git
[GPL3+]: LICENSE

[Build Status Badge]: http://img.shields.io/codeship/???.svg
[Codacy Badge]: https://www.codacy.com/project/badge/3c57f48168a9410183fc82c33c103513
[License Badge]: https://img.shields.io/badge/License-GPL3%2B-lightgray.svg
[Project Stage Badge: Experimental]: http://img.shields.io/badge/Project%20Stage-Experimental-yellow.svg
[Version Badge]: http://img.shields.io/github/tag/potherca/ssocr-ws
