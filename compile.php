<?php
include 'parse.php';
function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}
$config = json_decode(file_get_contents('config.json'), true);
$cname = $config['copyright'];
$bname = $config['blogname'];
function generateFromTemplate($bct, $title) {
    global $bname, $cname;
    $template = file_get_contents('template.html');
    $template = @str_replace('{{BLOGNAME}}', $bname, $template);
    $template = @str_replace('{{TITLE}}', $title, $template);
    $template = @str_replace('{{CONTENT}}', $bct, $template);
    $template = @str_replace('{{COPYRIGHT}}', $cname, $template);
    return $template;
}
function generateFromTemplateHP($bct, $title) {
    global $bname, $cname;
    $template = file_get_contents('homepage.html');
    $template = @str_replace('{{BLOGNAME}}', $bname, $template);
    $template = @str_replace('{{TITLE}}', $title, $template);
    $template = @str_replace('{{CONTENT}}', $bct, $template);
    $template = @str_replace('{{COPYRIGHT}}', $cname, $template);
    return $template;
}
if (is_dir('result')) {
    foreach(scandir('result') as $file) {
        @unlink("result/$file");
    }
    rmdir('result');
}
mkdir('result');

$pages = [];

foreach (scandir('blog') as $file) {
    if (endsWith($file, '.md')) {
        $fc = file_get_contents("blog/$file");
        $Parsedown = new Parsedown();
        $html = $Parsedown->text($fc);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $title = $dom->getElementsByTagName('h1')['0']->nodeValue;
        $html = generateFromTemplate($html, $title);

        file_put_contents('result/' . str_replace('.md', '.html', $file), $html);
        $pages[str_replace('.md', '.html', $file)] = $title;
    }
}

$indexhtml = '';

foreach ($pages as $page => $title) {
    $indexhtml .= '<a class="post" href="' . htmlspecialchars($page) . '">' . htmlspecialchars($title) . '</a>';
}

file_put_contents('result/index.html', generateFromTemplateHP($indexhtml, 'Home'));