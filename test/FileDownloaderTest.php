<?php

class FileDownloaderTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        var_dump('toto');
    }

    public function testDownload()
    {
        $urlMedia = 'http://blog.sociallymap.com/app/uploads/2015/11/Rediffuser-une-publication-en-différé1.png';
        $destination = "tmp/destination.png";

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
        $destination = 'tmp/file.jpg';

        $fileDownloader = new FileDownloader();

        $localFilename = $fileDownloader->download($urlMedia, $destination);
    }

    public function testRedirectionsAreFollowed()
    {
        $urlMedia = 'http://bit.ly/1m6zdSp';
        $destination = "tmp/redirection_destination.png";

        $fileDownloader = new FileDownloader();

        $localFilename = $fileDownloader->download($urlMedia, $destination);

        $this->assertFileExists($destination);
    }
}
