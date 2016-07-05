<?php

class Migrations
{
    var $template;
    var $content;
    var $logs = array();

    function load_template($file)
    {
        $this->template = file_get_contents($file);
    }

    function content($content)
    {
        $this->content = $content;
    }

    function parse()
    {
        $template = str_replace(
            array('{version_from}', '{version_to}', '{script}', '{content}'),
            array('2.0.3', '2.0.4', 'cn203_204.php', $this->content), $this->template);

        return $template;
    }

    function add_log($log)
    {
        $this->logs[] = '['.date('Y-m-d H:i:s') . '] ' . $log;
    }

    function get_logs()
    {
        return join("<br/>", $this->logs);
    }

    function get_directories($path)
    {
        $dh = opendir($path);
        while (false !== ($filename = readdir($dh))) {

            if ($filename == '.' || $filename == '..')
                continue;

            $files[] = $filename;
        }

        return $files;
    }
}

