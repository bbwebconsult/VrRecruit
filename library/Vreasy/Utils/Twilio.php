<?php

namespace Vreasy\Utils;

class Twilio
{
    
    protected static $positives = ['yes', 'si', 'ok'];
    protected static $negatives = ['no'];
    
    const ANSWER_NEGATIVE = -1;
    const ANSWER_UNKNOWN = 0;
    const ANSWER_POSITIVE = 1;

    //Because the answer are usually short, only allow 1 letter distance
    protected static $max_levenshtein_distance = 1;
    
    /**
     * Decodes a message to see if it is positive or negative
     * @param string $message The message to decode
     * @return result 1 if positive, -1 if negative. 0 if the message was not understood
     */
    public static function decodeAnswer($message)
    {
        //remove accents
        $message = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $message));

        //First try to find exact answers
        if(in_array($message, self::$positives))
            return self::ANSWER_POSITIVE;
        elseif(in_array($message, self::$negatives))
            return self::ANSWER_NEGATIVE;
        
        //No exact answer was found, let's try to detect typos with Levenshtein distance
        $bestMatchDistance = null;
        $bestMatchAnswer = self::ANSWER_UNKNOWN;
        
        foreach(array_merge(self::$positives, self::$negatives) as $word)
        {
            $dist = levenshtein($message, $word);
            if($dist <= self::$max_levenshtein_distance && ( !isset($bestMatchDistance) || $dist < $bestMatchDistance))
            {
                
                $bestMatchDistance = $dist;
                if(in_array($word, self::$positives))
                    $bestMatchAnswer = self::ANSWER_POSITIVE;
                else
                    $bestMatchAnswer = self::ANSWER_NEGATIVE;
            }
        }
        
        return $bestMatchAnswer;
        
    }
}
