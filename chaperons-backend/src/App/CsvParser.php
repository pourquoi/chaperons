<?php

namespace App;

trait CsvParser
{
    /**
     * Return the csv file delimiter
     *
     * @param string $path
     * @param int $min_cols
     * @return string
     */
    public function detectDelimiter($path, $min_cols=1) {
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $csv = new \SplFileObject($path);
        $header = $csv->fgets();
        $first_line = $csv->fgets();

        $delimiters = [',', ';', '\t', '|', '^'];

        $delimiter = null;

        foreach($delimiters as $d) {
            $cols1 = explode($d, $header);
            $cols2 = explode($d, $first_line);

            if(sizeof($cols1) <= sizeof($cols2) && sizeof($cols1) >= $min_cols) {
                $delimiter = $d;
                break;
            }
        }

        $csv = null;

        if(!$delimiter) {
            throw new \Exception('Could not detect the csv delimiter');

        }

        return $delimiter;
    }

    /**
     * automatic convertion windows-1250 and iso-8859-2 info utf-8 string
     */
    private function autoUTF(string $s): string
    {
        // detect UTF-8
        if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s))
            return $s;

        // detect WINDOWS-1250
        if (preg_match('#[\x7F-\x9F\xBC]#', $s))
            return iconv('WINDOWS-1250', 'UTF-8', $s);

        // assume ISO-8859-2
        return iconv('ISO-8859-2', 'UTF-8', $s);
    }
}