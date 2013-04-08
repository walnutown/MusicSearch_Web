<?php 
ini_set('default_charset', 'UTF-16'); 
header('Content-Type:text/xml; charset=UTF-16');
error_reporting( E_ALL&~E_NOTICE );

$origin_url = "http://www.allmusic.com/search/";
$search     = $_GET['title'];
/*         echo $search; */
$select     = $_GET['type'];
/*         echo $select; */
$url        = $origin_url.$select."/".urlencode($search); 
/*         echo $url;   */
$html       = file_get_contents($url);
/*         echo $html; */
$num        = 0;
$result_match;

//----------------------------- Functions --------------------------------------//          
function printList($value)
{
    $text = "";
    if($value == "NA")
    {
        $text .= "NA";
    }
    else
    {
        for($j = 0; $j < count($value); $j++)
        {
            if($j < (count($value)-1))
            {
                $text .= $value[$j]."/";
            }
            else
            {
                $text .= $value[$j];
            }
        }
    }
    return $text;
}
            
function artistSearch()
{
    global $result_match,$num;
    $artistArr  = array();
    $xml_txt   = "";
        
    for($i = 0; $i < $num; $i++)
    {
        $artist             = array();
        $artist['genre']    = "NA";
        $artist['year']     = "NA";
        $artist['image']    = "NA";
        
        $regex_artist_name  = "/<div\sclass=\"name\">[\s]*<a\shref=\"(.*?)\"\s[^>]*>(.*?)<\/a>/is";
        preg_match_all($regex_artist_name, $result_match[$i][1],$artist_match_name,PREG_SET_ORDER);
        //print_r($artist_match_name);
    
        $regex_artist_image = "/<img(.*)src=\"(.*?)\"\s/is";
        preg_match_all($regex_artist_image, $result_match[$i][1],$artist_match_image,PREG_SET_ORDER);
        //print_r($artist_match_image);
    
        $regex_artist_info  = "/<div\sclass=\"info\">[\s]*(.*?)[\s]*<br\/>[\s]*(.*?)[\s]*<\/div>/is";
        preg_match_all($regex_artist_info, $result_match[$i][1],$artist_match_info,PREG_SET_ORDER);
        //print_r($artist_match_info);
    
        $artist['name']     = $artist_match_name[0][2];
        $artist['details']  = $artist_match_name[0][1];
        if($artist_match_info[0][1] != null)
        {
            $artist['genre']= $artist_match_info[0][1];
        }
        if($artist_match_info[0][2] != null)
        {
            $artist['year'] = $artist_match_info[0][2];
        }
        if($artist_match_image != null)
        {
            $artist['image']= $artist_match_image[0][2];
        }
                            
        $artistArr[$i] = $artist;
    }
    //print_r($artistArr);
    
    for($i = 0; $i < $num; $i++)
    {
        $xml_txt .= "<result image=";
        $xml_txt .= "\"".htmlspecialchars($artistArr[$i]['image'])."\" ";
        $xml_txt .= "name=\"".htmlspecialchars($artistArr[$i]['name'])."\" ";
        $xml_txt .= "genre=\"".htmlspecialchars($artistArr[$i]['genre'])."\" ";
        $xml_txt .= "year=\"".htmlspecialchars($artistArr[$i]['year'])."\" ";
        $xml_txt .= "details=\"".htmlspecialchars($artistArr[$i]['details'])."\"";
        $xml_txt .= "/>";
    }
    echo $xml_txt;     
}

function albumSearch()
{
    global $result_match,$num;
    $albumArr   = array();
    $xml_txt   = "";
        
    for($i = 0; $i < $num; $i++)
    {
        $album  = array();
        $album['genre']     = "NA";
        $album['year']      = "NA";
        $album['image']     = "NA";
        $album['artist']    = "NA";
        
        $regex_album_title  = "/<div\sclass=\"title\">[\s]*<a\shref=\"(.*?)\"\s[^>]*>(.*?)<\/a>/is";
        preg_match_all($regex_album_title, $result_match[$i][1],$album_match_title,PREG_SET_ORDER);
        //print_r($album_match_title);
    
        $regex_album_image  = "/<img(.*)src=\"(.*?)\"\s/is";
        preg_match_all($regex_album_image, $result_match[$i][1],$album_match_image,PREG_SET_ORDER);
        //print_r($album_match_image);
    
        $regex_album_info   = "/<div\sclass=\"info\">[\s]*(.*?)[\s]*<br\/>[\s]*(.*?)[\s]*<\/div>/is";
        preg_match_all($regex_album_info, $result_match[$i][1],$album_match_info,PREG_SET_ORDER);
        //print_r($album_match_info);
        
        $regex_album_detail = "/<div\sclass=\"artist\">(.*?)<\/div>/is";
        preg_match_all($regex_album_detail, $result_match[$i][1],$album_match_detail,PREG_SET_ORDER);
        //print_r($album_match_detail);
        
        if($album_match_detail != null)
        {
            $artistList = array();
            $regex_album_artist = "/<a\shref=\"(.*?)\">(.*?)<\/a>/";
            preg_match_all($regex_album_artist, $album_match_detail[0][1],$album_match_artist,PREG_SET_ORDER);
            //print_r($album_match_artist);
            
            for($j = 0; $j <count($album_match_artist);$j++)
            {                   
                array_push($artistList,$album_match_artist[$j][2]);
            }
            $album['artist'] = $artistList;
        }
                        
        $album['title']     = $album_match_title[0][2];
        $album['details']   = $album_match_title[0][1];
        if($album_match_info[0][2] != null)
        {
            $album['genre'] = $album_match_info[0][2];
        }
        if($album_match_info[0][1] != null)
        {
            $album['year']  = $album_match_info[0][1];
        }
        if($album_match_image != null)
        {
            $album['image'] = $album_match_image[0][2];
        }
                            
        $albumArr[$i] = $album;
    
    }
    //print_r($albumArr);
    
    for($i = 0; $i < $num; $i++)
    {
        $xml_txt .= "<result image=";
        $xml_txt .= "\"".htmlspecialchars($albumArr[$i]['image'])."\" ";
        $xml_txt .= "title=\"".htmlspecialchars($albumArr[$i]['title'])."\" ";
        $xml_txt .= "artist=\"".htmlspecialchars(printList($albumArr[$i]['artist']))."\" ";
        $xml_txt .= "genre=\"".htmlspecialchars($albumArr[$i]['genre'])."\" ";
        $xml_txt .= "year=\"".htmlspecialchars($albumArr[$i]['year'])."\" ";
        $xml_txt .= "details=\"".htmlspecialchars($albumArr[$i]['details'])."\"";
        $xml_txt .= "/>";
    }
    echo $xml_txt;   
}
function songSearch()
{
    global $result_match,$num;
    $songArr    = array();
    $xml_txt   = "";
        
    for($i = 0; $i < $num; $i++)
    {
        $song               = array();
        $song['sample']     = "NA";
        $song['performer']  = "NA";
        $song['composer']   = "NA";
        
        $regex_song_title   = "/<div\sclass=\"title\">[\s]*<a\shref=\"(.*?)\">(.*?)\<\/a>/is";
        preg_match_all($regex_song_title, $result_match[$i][1],$song_match_title,PREG_SET_ORDER);
        //print_r($song_match_title);
    
        $regex_song_sample  = "/<a\shref=\"(.*?)\"\stitle[^>]*><\/a>/is";
        preg_match_all($regex_song_sample, $result_match[$i][1],$song_match_sample,PREG_SET_ORDER);
        //print_r($song_match_sample);
    
        $regex_song_info    = "/<div\sclass=\"info\">[\s]*<br\/>[\s]*Composed\sby(.*)<\/div>/is";
        preg_match_all($regex_song_info, $result_match[$i][1],$song_match_info,PREG_SET_ORDER);
        //print_r($song_match_info);
        
        $regex_song_info2   = "/<span\sclass=\"performer\">[\s]*by(.*)<\/span>/is";
        preg_match_all($regex_song_info2, $result_match[$i][1],$song_match_info2,PREG_SET_ORDER);
        //print_r($song_match_info2);
        
        if($song_match_info != null)
        {
            $composer = array();
            $regex_song_composer = "/<a\shref=\"(.*?)\">(.*?)<\/a>/";
            preg_match_all($regex_song_composer, $song_match_info[0][1],$song_match_composer,PREG_SET_ORDER);
            //print_r($song_match_composer);
            
            for($j = 0; $j <count($song_match_composer);$j++)
            {                   
                array_push($composer,$song_match_composer[$j][2]);
                
            }
            $song['composer'] = $composer;
        }
        
        if($song_match_info2 != null)
        {
            $performer = array();
            $regex_song_performer = "/<a\shref=\"(.*?)\">(.*?)<\/a>/";
            preg_match_all($regex_song_performer, $song_match_info2[0][1],$song_match_performer,PREG_SET_ORDER);
            //print_r($song_match_performer);
            
            for($k = 0; $k <count($song_match_performer);$k++)
            {                   
                array_push($performer,$song_match_performer[$k][2]);
            }
            $song['performer'] = $performer;
        }
        
        $song['title'] = $song_match_title[0][2];
        $song['details'] = $song_match_title[0][1];
        if($song_match_sample != null)
        {
            $song['sample'] = $song_match_sample[0][1];
        }
                            
        $songArr[$i] = $song;
    }
    //print_r($songArr);
    //print_r($song_match_info);
    
    for($i = 0; $i < $num; $i++)
    {
        $xml_txt .= "<result sample=\"".htmlspecialchars($songArr[$i]['sample'])."\" ";
        $xml_txt .= "title=\"".htmlspecialchars($songArr[$i]['title'])."\" ";
        $xml_txt .= "performer=\"".htmlspecialchars(printList($songArr[$i]['performer']))."\" ";
        $xml_txt .= "composer=\"".htmlspecialchars(printList($songArr[$i]['composer']))."\" ";
        $xml_txt .= "details=\"".htmlspecialchars($songArr[$i]['details'])."\"";
        $xml_txt .= "/>";
    }
    echo $xml_txt;

}

//-------------------------------------------Main -------------------------------------------//     
echo "<?xml version=\"1.0\" encoding=\"UTF-16\"?> ";
echo "<results> ";

$regex_count = "/<tr\sclass=\"search-result\s[artist|album|song]+\">(.*?)<\/tr>/is";
if(preg_match_all($regex_count,$html,$result_match,PREG_SET_ORDER))
{   
    //print_r($result_match);
    $num = count($result_match);    
}
else
{
    //echo "<h1>No Discography found</h1>";
}   

if($num > 0)
{
    if($select == "artist")
    {
        artistSearch();
    }
    else if ($select == "album")
    {
        albumSearch();
    }
    else if ($select == "song")
    {
        songSearch();
    }   
} 
echo "</results>";  
?>