<?php declare(strict_types=1);

namespace Interposition\Context\Test;

class TableRenderer
{
    public static function render(array $data): string
    {
        $maxRowsLen = [];
        $linesCount = count($data);

        for ($i = 0; $i < $linesCount; $i++){
            $line = $data[$i];

            if(!is_array($line)){
                throw new \InvalidArgumentException('One of array children is not line.');
            }

            $cellNumber = 0;

            foreach ($line as $cell){
                $len = strlen((string)$cell);

                if(!isset($maxRowsLen[$cellNumber]) || $maxRowsLen[$cellNumber] < $len){
                    $maxRowsLen[$cellNumber] = $len;
                }

                $cellNumber++;
            }
        }

        [$top, $center, $bottom] = self::buildBorders($maxRowsLen);

        $out   = [$top];

        for ($i = 0; $i < $linesCount; $i++){
            $cellNumber = 0;

            $line = $data[$i];

            foreach ($line as &$cell){

                $cell = str_pad((string)$cell, $maxRowsLen[$cellNumber], ' ');

                $cellNumber++;
            }

            $out[] = '| '.implode(' | ', $line).' |';

            if($i < $linesCount - 1){
                $out[] = $center;
            }
        }

        $out[] = $bottom;

        return implode("\n", $out);
    }

    private static function buildBorders(array $maxRowsLen): array
    {
        foreach ($maxRowsLen as $key => $length){
            $maxRowsLen[$key] = str_repeat('─', $length);
        }

        return [
            '┌─'.implode('─┬─', $maxRowsLen).'─┐',
            '|─'.implode('─|─', $maxRowsLen).'─|',
            '└─'.implode('─┴─', $maxRowsLen).'─┘',
        ];
    }
}
