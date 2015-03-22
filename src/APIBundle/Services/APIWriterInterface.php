<?php
namespace APIBundle\Services;

/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/22/2015
 * Time: 5:56 PM
 */

interface APIWriterInterface {

    public function __construct($rootDir, $filePath);

    public function writeArray($convertedArray);

    public function writeDocumentHead();

    public function writeDocumentFooter();
}