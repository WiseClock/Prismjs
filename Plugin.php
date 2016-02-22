<?php
/**
 * Prismjs代码高亮插件
 * 
 * @package Prismjs
 * @author WiseClock
 * @version 1.0.7
 * @dependence 14.10.10
 * @link http://wiseclock.ca
 */
class Prismjs_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Prismjs_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('Prismjs_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Archive')->header = array('Prismjs_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Prismjs_Plugin', 'footer');
    }

    public static function deactivate() {}
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $help = new Typecho_Widget_Helper_Layout('div', array('style' => 'color: #999; background-color: #eee; border-radius: 10px; padding: 10px;'));
        $help->html("<strong>使用方法</strong><pre><small>```c#\nConsole.Write(\"Hello World!\");\n```\n或行内代码\n`js console.log(\"Hi!\");`</small></pre>");
        $form->addItem($help);

        $themes = array_map('basename', glob(dirname(__FILE__) . '/themes/*.css'));
        $themes = array_combine($themes, $themes);
        $theme = new Typecho_Widget_Helper_Form_Element_Select('theme', $themes, 'prism-coy.css', _t('代码样式'));
        $form->addInput($theme->addRule('enum', _t('必须选择样式'), $themes));

        $showLineNumber = new Typecho_Widget_Helper_Form_Element_Checkbox('showln', array('showln' => _t('显示行号')), array('showln'), _t('是否在大段代码左侧显示行号'));
        $form->addInput($showLineNumber);

        $forceWrap = new Typecho_Widget_Helper_Form_Element_Checkbox('forceWrap', array('forceWrap' => _t('强制换行')), array('forceWrap'), _t('是否强制换行'));
        $form->addInput($forceWrap);

        $showLang = new Typecho_Widget_Helper_Form_Element_Checkbox('showlang', array('showlang' => _t('显示语言标签')), array('showlang'), _t('是否在大段代码右上角显示语言'));
        $form->addInput($showLang);
    }

    public static function header()
    {
        $themeUrl = Helper::options()->pluginUrl . '/Prismjs/themes/' . Helper::options()->plugin('Prismjs')->theme;
        echo '<link href="' . $themeUrl . '" rel="stylesheet" />';
        if (!Helper::options()->plugin('Prismjs')->showlang)
            echo "<style>.prism-show-language{display:none}</style>";
        if (Helper::options()->plugin('Prismjs')->forceWrap)
            echo '<link href="' . Helper::options()->pluginUrl . '/Prismjs/wrap-fix.css' . '" rel="stylesheet" />';
    }

    public static function footer()
    {
        if (Helper::options()->plugin('Prismjs')->showln)
            echo "<script>var pres = document.getElementsByTagName('pre');
                for (var i = 0; i < pres.length; i++)
                    if (pres[i].getElementsByTagName('code').length > 0)
                        pres[i].className  = 'line-numbers';
                </script>";
        $jsUrl = Helper::options()->pluginUrl . '/Prismjs/prism.js';
        echo '<script src="' . $jsUrl . '"></script>';
        if (Helper::options()->plugin('Prismjs')->forceWrap && Helper::options()->plugin('Prismjs')->showln)
            echo '<script defer="defer" src="' . Helper::options()->pluginUrl . '/Prismjs/line-number-wrap-fix.js' . '"></script>';
    }

    public static function parse($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;

        $text = preg_replace('/<code>(c#|py|yml|c\+\+|bat|as|js|markup|css|clike|javascript|actionscript|applescript|aspnet|bash|basic|batch|cpp|csharp|c|coffeescript|ruby|css-extras|go|groovy|java|latex|lua|markdown|objectivec|php|php-extras|powershell|python|sass|scss|sql|swift|yaml)\s/i', '<code class="language-$1">', $text);
        $text = preg_replace_callback('/<code class="language-([^"]+)">/i', function($m) { return '<code class="language-' . strtolower($m[1]) . '">'; }, $text);
        $text = str_replace('language-c#', 'language-csharp', $text);
        $text = str_replace('language-yml', 'language-yaml', $text);
        $text = str_replace('language-bat"', 'language-batch"', $text);
        $text = str_replace('language-c++', 'language-cpp', $text);
        $text = str_replace('language-as"', 'language-actionscript"', $text);
        $text = str_replace('language-js', 'language-javascript', $text);
        $text = str_replace('language-py"', 'language-python"', $text);
        $text = str_replace('<code>', '<code class="language-unknown">', $text);

        return $text;
    }
}
