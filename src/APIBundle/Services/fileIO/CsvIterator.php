<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/23/2015
 * Time: 9:53 AM
 */

namespace APIBundle\Services\fileIO;

use Iterator;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class CsvIterator  implements Iterator
{
    /**
     * Must be greater than the longest line (in characters) to be found in
     * the CSV file (allowing for trailing line-end characters).
     *
     * @var int
     */
    const ROW_LENGTH = 2048;

    /**
     * Resource file pointer
     */
    private $_filePointer;

    /**
     * Represents current element in iteration
     *
     * @var int
     */
    private $_currentElement;

    /**
     * Cumalitve row count of CSV data
     *
     * @var int
     */
    private $_rowCounter;

    /**
     * CSV column delimeter
     *
     * @var string
     */
    private $_delimiter;

    //array keys for printing human readable csv
    private $_keys;

    /**
     * Create an instance of the CsvFileIterator class.
     *
     * Throws InvalidArgumentException if CSV file (string $file)
     * does not exist.
     *
     * @param string $file The CSV file path.
     * @param string $delimiter The default delimeter is a single comma (,)
     */
    public function __construct($path, $file, $delimiter = ',', $keys = null)
    {
        $file = $path.$file;
        if (! file_exists($file))
            throw new InvalidArgumentException("{$file}");

        $this->_filePointer = fopen($file, 'rt');
        $this->_delimiter = $delimiter;
        if($keys)
            $this->_keys = $keys;
    }

    /*
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_rowCounter = 0;
        rewind($this->_filePointer);
    }

    /*
     * @see Iterator::current()
     */
    public function current()
    {
        $this->_currentElement =
            fgetcsv($this->_filePointer, self::ROW_LENGTH, $this->_delimiter);
        $this->_rowCounter ++;

        if($this->_keys)
            $this->_currentElement = array_combine($this->_keys, array_values($this->_currentElement));

        return $this->_currentElement;
    }

    /*
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_rowCounter;
    }

    /*
     * @see Iterator::next()
     */
    public function next()
    {
        if (!feof($this->_filePointer))
            return true;
        else
        return false;
    }

    /*
     * @see Iterator::valid()
     */
    public function valid()
    {
        if (! $this->next())
            fclose($this->_filePointer);
        return FALSE;
    }

}