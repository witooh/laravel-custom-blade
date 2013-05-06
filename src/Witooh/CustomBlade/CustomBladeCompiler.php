<?php
namespace Witooh\CustomBlade;

use Illuminate\View\Compilers\BladeCompiler;

class CustomBladeCompiler extends BladeCompiler
{

    /**
     * Array of opening and closing tags for echos.
     *
     * @var array
     */
    protected $contentTags = array('{%', '%}');

    /**
     * Enable/Disable Compression
     *
     * @var bool
     */
    protected $compress = true;

    /**
     * Set Blade Compiler to compress the content
     *
     * @param bool $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path)
    {
        $contents = $this->compileString($this->files->get($path));

        if (!is_null($this->cachePath)) {
            $this->files->put(
                $this->getCompiledPath($path),
                $this->compress ? $this->html_compress($contents) : $contents
            );
        }
    }

    /**
     * Compress html content
     *
     * @param string $html
     * @return string mixed
     */
    protected function html_compress($html)
    {
        preg_match_all(
            '!(&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;)!',
            $html,
            $pre
        ); #exclude pre or code tags<br />
        $html = preg_replace(
            '!&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;!',
            '#pre#',
            $html
        ); #removing all pre or code tags<br />
//        $html = preg_replace('<!--[ ^\[].+-->', '', $html);#removing HTML comments<br />
        $html = preg_replace('/[\r\n\t]+/', '', $html); #remove new lines, spaces, tabs<br />
        $html = preg_replace('/&gt;[\s]+&lt;/', '&gt;&lt;', $html); #remove new lines, spaces, tabs<br />
        $html = preg_replace('/[\s]+/', ' ', $html); #remove new lines, spaces, tabs<br />
        if (!empty($pre[0])) {
            foreach ($pre[0] as $tag) {
                $html = preg_replace('!#pre#!', $tag, $html, 1);
            }
        }

        #putting back pre|code tags<br />
        return $html;
    }
}