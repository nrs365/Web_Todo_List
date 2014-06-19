<?php

class Filestore {

    public $filename = '';

    function __construct($filename = '') 
    {
        $this->filename = $filename;
    }

    /**
     * Returns array of lines in $this->filename
     */
    function read_lines()
    {
       // todo list 
        $list_array = [];
        if (is_readable($this->filename) && filesize($this->filename) > 0) {
            $filesize = filesize($this->filename);
            $read = fopen($this->filename, 'r');
            $list_string = trim(fread($read, $filesize));
            fclose($read);
            $list_array = explode("\n", $list_string);
        }
        return $list_array; 
    }      

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    function write_lines($array)
    {
            // todolist
        $saved_file = fopen($this->filename, 'w');
        $list_string = implode("\n", $array);
        fwrite($saved_file, $list_string);
        fclose($saved_file);
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    function read_csv()
    {
        // address book
        $array = [];
        if (is_readable($this->filename) && filesize($this->filename) > 0) {
            $filesize = filesize($this->filename);
            $read = fopen($this->filename, 'r');
            while(!feof($read)) {
                $row = fgetcsv($read);
                if (!empty($row)){
                    $array[] = $row;
                }
            }
            fclose($read);
        }
        return $array;
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    function write_csv($array)
    {
        // address book
        $handle = fopen($this->filename,'w');
        foreach ($array as $entry) {
            fputcsv($handle, $entry);
        }
        fclose($handle);
    }    
}
?>