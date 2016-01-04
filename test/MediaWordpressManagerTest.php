<?php

class MediaWordpressManagerTest extends PHPUnit_Framework_TestCase
{
    public function testDownload()
    {
        $temporyFile = "../test/tmp/redirection_destination";
        $fileExtension = ".png";

        $mediaManager = new MediaWordpressManager();

        $mediaManager->integrateMediaToWordpress($temporyFile, $fileExtension);

        return 0;
    }
}
