<?php
namespace KDSM\APIBundle\Services\fileIO;

/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/22/2015
 * Time: 5:56 PM
 */

interface WriterInterface
{

    public function __construct($rootDir, $filePath);

    public function writeArray($convertedArray);

    public function writeDocumentHead();

    public function writeDocumentFooter();
}