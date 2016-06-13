<?php

$link = curl_init('Insert the News RSS Feed URL');
curl_setopt($link, CURLOPT_RETURNTRANSFER, true);
curl_setopt($link, CURLOPT_HEADER, 0);
$content = curl_exec($link);
curl_close($link);

$article = new SimpleXmlElement($content, LIBXML_NOCDATA);

str_replace(array('<\![CDATA[',']]>'), '', $article); //To get rid of <![CDATA...  in the description field

if(isset($article->channel))
{
    parsing($article);
} else {
    echo "Check the URL again";
}

function parsing($xml)
{	
        $file = fopen('filename.csv', 'a');
        $heading = array('Title','Description','Link');
        fputcsv($file,$heading);
        $total = count($xml->channel->item);
        for($i=0; $i<$total; $i++)
        {
            $title = preg_replace('/[^A-Za-z0-9\- ]/', '', $xml->channel->item[$i]->title); //To Remove special characters
			
            $description = html_entity_decode($xml->channel->item[$i]->description);
			$description = strip_tags($description); //To Remove HTML Tags Within description
            $description = preg_replace('/[^A-Za-z0-9\- ]/', '', $description); //To Remove special characters
			
			$link = $xml->channel->item[$i]->link;
			
			$info = array($title, $description, $link);
            
			fputcsv($file, $info);
        }

        fclose($file);
}

?>
