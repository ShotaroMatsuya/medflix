<?php

class  FormSanitizer
{


    public static function sanitizeFormString($inputText)
    {
        $inputText = strip_tags($inputText);  //htmlタグを取り除く
        $inputText = str_replace(" ", "", $inputText); //空文字を取り除く
        //$inputText = trim($inputText); //空文字を取り除く
        $inputText = strtolower($inputText); //すべてlowercaseに
        $inputText = ucfirst($inputText); //最初の文字だけuppercaseに
        return $inputText;
    }
    public static function sanitizeFormUsername($inputText)
    {
        $inputText = strip_tags($inputText);  //htmlタグを取り除く
        $inputText = str_replace(" ", "", $inputText); //空文字を取り除く
        return $inputText;
    }
    public static function sanitizeFormPassword($inputText)
    {
        $inputText = strip_tags($inputText);  //htmlタグを取り除く
        return $inputText;
    }
    public static function sanitizeFormEmail($inputText)
    {
        $inputText = strip_tags($inputText);  //htmlタグを取り除く
        $inputText = str_replace(" ", "", $inputText); //空文字を取り除く
        return $inputText;
    }
}
