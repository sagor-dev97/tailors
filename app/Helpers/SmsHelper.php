<?php

if (!function_exists('summer_to_sms')) {

    function summer_to_sms($html)
    {
        if (!$html) return '';

        // convert line breaks
        $html = str_replace(
            ['<br>', '<br/>', '<br />', '</p>'],
            "\n",
            $html
        );

        // convert list
        $html = str_replace(
            ['<li>', '</li>'],
            ["\n• ", ""],
            $html
        );

        $text = strip_tags($html);
        $text = html_entity_decode($text);

        return trim($text);
    }
}
