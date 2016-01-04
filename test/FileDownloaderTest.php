<?php

class FileDownloaderTest extends PHPUnit_Framework_TestCase
{
    public function testDownload()
    {
        $urlMedia = 'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4';
        $destination = "tmp/fichier";

        $fileDownloader = new FileDownloader();

        $localFilename = $fileDownloader->download($urlMedia, $destination);

        $this->assertFileExists($destination);
    }

    /**
     * @expectedException Exception
     */
    public function testIfFileDontExistsResultInException()
    {
        $urlMedia = 'http://blog.sociallymap.com/inexistent-file.jpg';
        $destination = 'tmp/file';

        $fileDownloader = new FileDownloader();

        $localFilename = $fileDownloader->download($urlMedia, $destination);
    }

    public function testRedirectionsAreFollowed()
    {
        $urlMedia = 'http://bit.ly/1m6zdSp';
        $destination = "tmp/redirection_destination";

        $fileDownloader = new FileDownloader();

        $localFilename = $fileDownloader->download($urlMedia, $destination);

        $this->assertFileExists($destination);
    }
}
