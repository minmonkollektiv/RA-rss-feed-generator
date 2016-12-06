<?php

//includen der Simple html DOM Parsers
include_once('simple_html_dom.php');

//zusammensetzten der zu Parsenden URL -> die Aktuellen Dates, f�r diesen Tag, auf RA
//http://www.residentadvisor.net/events.aspx?ai=34&v=day&mn=10&yr=2014&dy=16
$cityid = 34; //= Berlin
$url = 'http://www.residentadvisor.net/events.aspx?ai='. $cityid .'&v=day&mn=' . date(n) . '&yr='. date(Y).'&dy='.date(j);
$html = file_get_html($url);

//finden des Tags welches die gesuchten Informationen enth�lt
$items = $html->find('article[class=event-item  ##clearfix]');

//durchgehen der items und extraieren der Informatioen. Es wird das Eltern Tag angesprochen und davon Kind 1 und 2. Der Text in der klammer wird extrauiert und ins Array geschrieben
foreach($items as $post) {
	# remember comments count as nodes
	$eventname[] = array($post->children(1)->href, //LINK
	$post->children(1)->children(0)->src, //Bild
	$post->children(2)->children(0)->children(0)->innertext, //titel
	$post->children(2)->children(0)->children(1)->children(0)->innertext,   //Club
	$post->children(2)->children(1)->innertext, //Beschreibung
	$post->children(0)->children(0)->innertext //datetime
	);
}

//Ausgabe als XML/RSS feed
echo 	'<?xml version="1.0" encoding="UTF-8"?>
		<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
		<channel>
		<title>Fresh Partydates</title>
		<link>http://www.galonga.de/</link>
        <description>xD</description>
        <lastBuildDate>' , date('D, d M Y H:i:s O'), '</lastBuildDate>
		<image>
            <url>./images/logo.jpg</url>
            <title>Galonga</title>
            <link>http://www.galonga.de/</link>
            <description>Fresh partydates feed</description>
        </image>
		<language>de</language>' , "\n";
				foreach($eventname as $item) {

				if(!(empty($item[4]) && !isset($item[4]))){  //leere Lineups raus nehmen
					$item[4] = "Lineup: " . $item[4];
				 }
					echo '		<item>', "\n",
					//entfernen von HTML Tags und anzeigen der entsprechenden HTMLspecChars
					'            <title>', htmlspecialchars(strip_tags($item[2])) ,' @ ',htmlspecialchars(strip_tags($item[3])) ,  '</title>', "\n",
					'            <link>http://www.residentadvisor.net' , htmlspecialchars(strip_tags($item[0]))  , '</link>', "\n",
					//Aktuelles Datum als pubDate setzten (RSS konform)
					'            <description><![CDATA[<img src="http://www.residentadvisor.net', htmlspecialchars(strip_tags($item[1])),'" align="middle"><br>]]>', htmlspecialchars(strip_tags($item[4])) , '</description>', "\n",
					'            <pubDate>' , htmlspecialchars(strip_tags($item[5])) , '</pubDate>', "\n",
					'		</item>';
					echo "\n";
					}
			echo '		</channel>
		</rss>';
?>
