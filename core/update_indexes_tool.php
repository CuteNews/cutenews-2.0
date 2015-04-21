<?php

ini_set('max_execution_time', 0);

echo 'START'.PHP_EOL;

include dirname(__FILE__).'/init.php';

/**
 * @desc Update indexes
 * @warning Call directy this file.
 */

function load_archive_index($files)
{   
    $arhive_index=array();
    foreach ($files as $idx)
    {
        $arhive_index[$idx] = db_index_load(pathinfo($idx,PATHINFO_FILENAME));
    }    
    
    return $arhive_index;
}

function is_news_inarchive($nid, $archive_index)
{    
    foreach ($archive_index as $file=>$val)
    {                     
        foreach ($val as $id=>$data)
        {
            if($nid==$id)
            {
                return substr(pathinfo($file,PATHINFO_FILENAME),8);
            }
        }
    }
    
    return FALSE;
}

$cdata_news = cn_path_construct(SERVDIR,'cdata','news');
// Clean unsuccessful
$indexes=array(
    SERVDIR . $cdata_news . 'cat.idx',
    SERVDIR . $cdata_news . 'date.idx',
    SERVDIR . $cdata_news . 'so_news.idx',
    SERVDIR . $cdata_news . 'so_draft.idx',
    SERVDIR . $cdata_news . 'tags.idx',
    SERVDIR . $cdata_news . 'users.idx',
    SERVDIR . $cdata_news . 'iactive.txt',
    SERVDIR . $cdata_news . 'idraft.txt',
    SERVDIR . $cdata_news . 'archive.txt'
);

$archive_files = scan_dir($cdata_news, '^archive-.*\.txt'); 

// Load archives before deleted
$archive_data = load_archive_index($archive_files);

foreach ($archive_files as $afile)
{    
    unlink($cdata_news.$afile);
}

// Remove old indexes
foreach($indexes as $index)
{
    if(file_exists($index))
    {
        unlink($index);
    }
}

// Scan news file
$files = scan_dir(cn_path_construct(SERVDIR,'cdata','news'), '\d+-\d+-\d+');

// Add overall news
$news_index =array();
$draft_index=array();
$archive_index =array();
foreach ($files as $news_file)
{
    $dt = cn_touch_get(cn_path_construct(SERVDIR,'cdata','news').$news_file);    
    foreach ($dt as $id => $data)
    {            
        // checks news existing in archive
        if($afn=  is_news_inarchive($id, $archive_data))
        {            
            $archive_index[$afn][$id] = db_index_create($data);
        }                                
        // checks draft
        if($data['st']=='d')
        {
            $draft_index[$id] = db_index_create($data);
        }
        else
        {
            $news_index[$id] =  db_index_create($data);
        }        
    }         
}

db_index_save($draft_index,'draft');
db_index_save($news_index);

db_index_update_overall();

foreach ($archive_index as $file=>$ind)
{    
    db_index_save($ind,'archive-'.$file);
    $min=min(array_keys($ind));
    $max=max(array_keys($ind));
    $cnt=  count($ind);
    db_archive_meta_update($file,$min,$max,$cnt);    
    db_index_update_overall('archive-'.$file);
}

echo "UPDATE SUCCESS.".PHP_EOL;